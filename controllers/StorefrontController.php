<?php

require_once "models/User.php";
require_once "models/Game.php";
require_once "models/Build.php";
require_once "models/Developer.php";
require_once "controllers/ViewController.php";
require_once "controllers/GameController.php";
require_once "helpers/OrderHelper.php";
require_once "helpers/Token.php";
require_once "helpers/s3.php";

class StorefrontController
{
    // --- Home ---
    public static function showHome()
    {
        $games = Game::pickRandom(10);
        $users = User::getCount();

        ViewController::render("home/index", [
            "showcaseGames" => $games,
            "users" => $users
        ]);
    }

    // --- Store & Search ---
    public static function showHub()
    {
        GameController::showStore();
    }

    public static function showSearch()
    {
        $searchQuery = $_GET["search"] ?? "";
        $genre = $_GET["genre"] ?? "";
        $features = isset($_GET["features"]) && $_GET["features"] !== "" ? explode(",", $_GET["features"]) : [];
        $page = $_GET["page"] ?? 1;
        GameController::showSearch($searchQuery, $genre, $features, $page);
    }

    public static function showGame($gameId)
    {
        global $router;
        $result = GameController::openGame($gameId);
        if ($result === false) {
            $router->trigger404();
            exit();
        }
    }

    public static function showCart()
    {
        if (!isset($_SESSION["user"])) {
            header("Location: /login");
            exit();
        }

        if (!OrderHelper::getOrder()) {
            header("Location: /store");
            exit();
        }

        ViewController::render('store/cart', [
            'cartItems' => OrderHelper::getInstances(),
            'totalPrice' => OrderHelper::getTotal(),
            'stripePublicKey' => $_ENV["STRIPE_PUBLIC_KEY"]
        ]);
    }

    // --- Library (API & Views) ---
    public static function apiGetGames()
    {
        if (!isset($_SESSION["user"])) {
            http_response_code(401);
            echo json_encode(["status" => 401, "message" => "User not logged in"]);
            exit();
        }

        $user = User::getById($_SESSION["user"]["id"]);
        $games = $user->getAdquiredGames();

        $data = array_map(fn($g) => [
            "id" => $g->id,
            "title" => $g->title,
            "isDeveloper" => $g->getDeveloper()->getOwner()->id == $user->id,
        ], $games);

        echo json_encode(["data" => $data]);
        exit();
    }

    public static function apiGetGameDetails($id)
    {
        if (!isset($_SESSION["user"])) {
            http_response_code(401);
            echo json_encode(["status" => 401, "message" => "User not logged in"]);
            exit();
        }

        $user = User::getById($_SESSION["user"]["id"]);
        if (!$user->hasAdquiredGame($id)) {
            http_response_code(401);
            echo json_encode(["status" => 401, "message" => "User has not adquired this game"]);
            exit();
        }

        $game = Game::getById($id);
        $features = array_map(fn($f) => [
            "icon" => $f->icon,
            "name" => $f->name,
            "tint" => $f->tint,
        ], $game->getFeatures());

        $achievements = array_map(function ($a) use ($user, $game) {
            $unlockedOn = "";
            $has = $user->hasUnlockedAchievement($game->id, $a->id, $unlockedOn);
            return [
                "name" => $a->name,
                "description" => $a->description,
                "icon" => $has ? $a->icon : ($a->locked_icon ?? $a->icon),
                "unlockedOn" => $unlockedOn,
            ];
        }, $game->getAchievements());

        $builds = array_map(fn($b) => [
            "version" => $b->version,
            "date" => $b->release_date,
            "patchNotes" => $b->patch_notes,
        ], $game->getBuilds());

        echo json_encode([
            "data" => [
                "id" => $game->id,
                "title" => $game->title,
                "features" => $features,
                "achievements" => $achievements,
                "builds" => $builds,
                "isDeveloper" => $game->getDeveloper()->getOwner()->id == $user->id,
            ]
        ]);
        exit();
    }

    public static function apiGetDownloadToken($gameid, $version)
    {
        if (!isset($_SESSION["user"])) {
            http_response_code(401);
            echo json_encode(["status" => 401, "message" => "User not logged in"]);
            exit();
        }

        $user = User::getById($_SESSION["user"]["id"]);
        $game = Game::getById($gameid);
        if (!$game || !$user->hasAdquiredGame($gameid)) {
            http_response_code(404);
            echo json_encode(["status" => 404, "message" => "Game not found or not owned"]);
            exit();
        }

        $build = ($version == "latest") ? $game->getLatestBuild() : $game->getBuildVersion($version);
        if (!$build) {
            http_response_code(404);
            echo json_encode(["status" => 404, "message" => "Build not found"]);
            exit();
        }

        $token = DownloadToken::createToken($user->id, $game->id, $build->version);
        echo json_encode([
            "token" => $token,
            "filename" => str_replace(" ", "_", $game->title) . "-ver-" . $build->version . ".zip",
            "url" => "/api/library/stream/" . urlencode($token),
        ]);
        exit();
    }

    public static function apiStreamBuild($token)
    {
        $token = urldecode($token);
        if (!DownloadToken::validateToken($token)) {
            http_response_code(403);
            echo json_encode(["error" => "Invalid or expired token"]);
            exit();
        }

        $data = DownloadToken::getTokenData($token);
        $game = Game::getById($data["game_id"]);
        $build = ($data["version"] === "latest") ? $game->getLatestBuild() : $game->getBuildVersion($data["version"]);

        $file = $build->getFile();
        if (!$file) {
            http_response_code(404);
            echo json_encode(["error" => "File not found"]);
            exit();
        }

        $filename = str_replace(" ", "_", $game->title) . "-ver-" . $build->version . ".zip";
        header("Content-Type: " . $file["type"]);
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        if (!empty($file["size"]))
            header("Content-Length: " . $file["size"]);

        if (ob_get_level())
            ob_end_clean();
        S3Helper::streamToClient($file);
        exit();
    }

    // --- Legal ---
    public static function showTerms()
    {
        ViewController::render('legal/terms', ['title' => 'Términos y condiciones de Orion']);
    }

    public static function showPrivacy()
    {
        ViewController::render('legal/privacy', ['title' => 'Política de privacidad de Orion']);
    }

    public static function showCookies()
    {
        ViewController::render('legal/cookies', ['title' => 'Política de cookies de Orion']);
    }

    public static function showRefund()
    {
        ViewController::render('legal/refund', ['title' => 'Política de reembolso y devoluciones de Orion']);
    }

    // --- Legacy/Utility API (from HomeController) ---
    public static function apiRandomGames()
    {
        $games = Game::pickRandom(10);
        $users = User::getCount();
        $gameList = array_map(fn($g) => ["id" => $g->id, "title" => $g->title], $games);
        echo json_encode(["showcaseGames" => $gameList, "users" => $users]);
        exit();
    }

    public static function apiDevelopersCount()
    {
        $developers = Developer::getCount();
        echo json_encode(["developers" => $developers]);
        exit();
    }
}

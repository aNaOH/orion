<?php

require_once "controllers/ViewController.php";
require_once "models/Developer.php";
require_once "models/Game.php";
require_once "models/GameFeature.php";
require_once "models/GameGenre.php";
require_once "models/Achievement.php";
require_once "models/Post.php";
require_once "models/GameNewsCategory.php";

class DevController
{
    public static function showIndex()
    {
        ViewController::render('dev/index', ['stripe_public_key' => $_ENV["STRIPE_PUBLIC_KEY"]]);
    }

    public static function showProfile($devId)
    {
        global $router;
        $developer = Developer::getById(intval($devId));
        if (is_null($developer)) {
            $router->trigger404();
            exit();
        }

        ViewController::render('dev/profile', ['developer' => $developer]);
    }

    public static function showPanelHome()
    {
        $user = User::getById($_SESSION["user"]["id"]);
        $developer = $user->getDeveloperInfo();
        ViewController::render('dev/panel/home', ['developer' => $developer]);
    }

    public static function showPanelGames()
    {
        $user = User::getById($_SESSION["user"]["id"]);
        $games = $user->getDeveloperInfo()->getGames();
        ViewController::render('dev/panel/games/index', ['games' => $games]);
    }

    public static function showPanelNewGame()
    {
        ViewController::render('dev/panel/games/new');
    }

    public static function showPanelGameStore($gameId)
    {
        global $router;
        $user = User::getById($_SESSION["user"]["id"]);
        $game = Game::getById($gameId);
        if (
            is_null($game) ||
            $game->getDeveloper()->getOwner()->id != $user->id
        ) {
            $router->trigger404();
            exit();
        }

        $features = GameFeature::getAll();
        $genres = GameGenre::getAll();

        ViewController::render('dev/panel/games/store/store', [
            'game' => $game,
            'features' => $features,
            'genres' => $genres
        ]);
    }

    public static function showPanelGameCommunity($gameId)
    {
        global $router;
        $user = User::getById($_SESSION["user"]["id"]);
        $game = Game::getById($gameId);
        if (
            is_null($game) ||
            $game->getDeveloper()->getOwner()->id != $user->id
        ) {
            $router->trigger404();
            exit();
        }
        ViewController::render('dev/panel/games/community/community', ['game' => $game]);
    }

    public static function showPanelGameAchievements($gameId)
    {
        global $router;
        $user = User::getById($_SESSION["user"]["id"]);
        $game = Game::getById($gameId);
        if (
            is_null($game) ||
            $game->getDeveloper()->getOwner()->id != $user->id
        ) {
            $router->trigger404();
            exit();
        }
        $achievements = $game->getAchievements();

        ViewController::render('dev/panel/games/community/achievements/index', [
            'game' => $game,
            'achievements' => $achievements
        ]);
    }

    public static function showPanelGameNewAchievement($gameId)
    {
        global $router;
        $user = User::getById($_SESSION["user"]["id"]);
        $game = Game::getById($gameId);
        if (
            is_null($game) ||
            $game->getDeveloper()->getOwner()->id != $user->id
        ) {
            $router->trigger404();
            exit();
        }
        $stats = $game->getStats();

        ViewController::render('dev/panel/games/community/achievements/create', [
            'game' => $game,
            'stats' => $stats
        ]);
    }

    public static function showPanelGameEditAchievement($gameId, $achievementId)
    {
        global $router;
        $user = User::getById($_SESSION["user"]["id"]);
        $game = Game::getById($gameId);
        if (
            is_null($game) ||
            $game->getDeveloper()->getOwner()->id != $user->id
        ) {
            $router->trigger404();
            exit();
        }
        $achievement = Achievement::getById($gameId, $achievementId);
        if (is_null($achievement)) {
            $router->trigger404();
            exit();
        }

        $stats = $game->getStats();

        ViewController::render('dev/panel/games/community/achievements/edit', [
            'game' => $game,
            'stats' => $stats,
            'achievement' => $achievement
        ]);
    }

    public static function showPanelGameNews($gameId)
    {
        global $router;
        $user = User::getById($_SESSION["user"]["id"]);
        $game = Game::getById($gameId);
        if (is_null($game) || $game->getDeveloper()->getOwner()->id != $user->id) {
            $router->trigger404();
            exit();
        }
        
        ViewController::render('dev/panel/games/community/news/index', [
            'game' => $game,
            'news' => Post::getAllByTypeAndGame(EPOST_TYPE::GAME_NEWS, $game->id)
        ]);
    }

    public static function showPanelGameNewNews($gameId)
    {
        global $router;
        $user = User::getById($_SESSION["user"]["id"]);
        $game = Game::getById($gameId);
        if (is_null($game) || $game->getDeveloper()->getOwner()->id != $user->id) {
            $router->trigger404();
            exit();
        }
        
        ViewController::render('dev/panel/games/community/news/create', [
            'game' => $game,
            'newscategories' => GameNewsCategory::getAll()
        ]);
    }

    public static function showPanelGameEditNews($gameId, $newsId)
    {
        global $router;
        $user = User::getById($_SESSION["user"]["id"]);
        $game = Game::getById($gameId);
        if (is_null($game) || $game->getDeveloper()->getOwner()->id != $user->id) {
            $router->trigger404();
            exit();
        }
        $new = Post::getById($newsId);
        if (is_null($new) || $new->game_id != $gameId || $new->author_id != $user->id || $new->type != EPOST_TYPE::GAME_NEWS) {
            $router->trigger404();
            exit();
        }

        ViewController::render('dev/panel/games/community/news/edit', [
            'game' => $game,
            'new' => $new,
            'newscategories' => GameNewsCategory::getAll()
        ]);
    }

    public static function handlePanel404()
    {
        header("HTTP/1.1 404 Not Found");
        ViewController::render('errors/404');
    }

    public static function apiPay()
    {
        if (!isset($_SESSION["user"])) {
            echo json_encode(["status" => 401, "error" => "User not logged in"]);
            exit();
        }

        $user = User::getById($_SESSION["user"]["id"]);
        if (!$user) {
            echo json_encode(["status" => 404, "error" => "User not found"]);
            exit();
        }

        if (!is_null($user->getDeveloperInfo())) {
            echo json_encode(["status" => 400, "error" => "User already has a developer account"]);
            exit();
        }

        StripeController::createDevAccountSession($user);
    }


    public static function apiSaveAccount()
    {
        $json = json_decode(file_get_contents("php://input"), true);
        FormHelper::ValidateToken($json["tript_token"] ?? "", "tript_token", ETOKEN_TYPE::COMMON);
        if (!isset($_SESSION["user"])) {
            echo json_encode(["status" => 401, "error" => "User not logged in"]);
            exit();
        }

        $user = User::getById($_SESSION["user"]["id"]);
        if (!$user || !is_null($user->getDeveloperInfo())) {
            echo json_encode(["status" => 400, "error" => "Error creating account"]);
            exit();
        }

        $user->addDeveloperInfo($json["name"]);
        echo json_encode(["status" => 200, "message" => "Developer account created"]);
        exit();
    }

    public static function apiCreateGame()
    {
        $json = json_decode(file_get_contents("php://input"), true);
        FormHelper::ValidateToken($json["tript_token"] ?? "", "tript_token", ETOKEN_TYPE::COMMON);
        $user = User::getById($_SESSION["user"]["id"]);
        $developer = $user->getDeveloperInfo();

        GameController::newGame(
            $json["title"],
            $json["shortDescription"],
            $json["asEditor"],
            $developer->id,
            $json["developerName"],
            $json["genre"] ?? 1
        );
        exit();
    }

    public static function apiGetFeatures()
    {
        $features = GameFeature::getAll();
        $featuresArray = array_map(fn($f) => [
            "id" => $f->id,
            "name" => $f->name,
            "icon" => $f->icon,
            "tint" => $f->tint,
        ], $features);

        echo json_encode(["status" => 200, "data" => $featuresArray]);
        exit();
    }

    public static function apiUpdateGameStore()
    {
        FormHelper::ValidateToken($_POST["tript_token"], "tript_token", ETOKEN_TYPE::DEVACTION);
        $rawData = json_decode($_POST["data"] ?? "{}", true);
        $gameID = $rawData["game"] ?? null;

        $game = Game::getById($gameID);
        if (is_null($game)) {
            header("HTTP/1.1 400 Bad Request");
            echo json_encode(["status" => 400, "message" => "Falta el ID del juego o no existe"]);
            exit();
        }

        $game->title = $rawData["title"] ?? "";
        $game->short_description = $rawData["shortDescription"] ?? "";
        $game->as_editor = $rawData["asEditor"] ?? false;
        $game->developer_name = $rawData["developerName"] ?? "";
        $game->description = $rawData["description"] ?? "";
        $game->base_price = $rawData["price"] ?? 0;
        $game->discount = ($rawData["discount"] ?? 0) / 100;
        $game->genre_id = $rawData["genre"] ?? 1;

        $game->setFeatures($rawData["features"] ?? []);
        $game->save();

        foreach (["coverFile" => EBUCKET_LOCATION::GAME_COVER, "thumbFile" => EBUCKET_LOCATION::GAME_THUMB, "iconFile" => EBUCKET_LOCATION::GAME_ICON] as $key => $loc) {
            if (isset($_FILES[$key])) {
                S3Helper::upload($loc, $game->id, null, $_FILES[$key]["type"], $_FILES[$key]["tmp_name"]);
            }
        }

        header("HTTP/1.1 200 OK");
        echo json_encode(["status" => 200, "message" => "Juego editado ( ID: " . strval($game->id) . " )"]);
        exit();
    }

    public static function apiUploadBuild()
    {
        FormHelper::ValidateToken($_POST["tript_token"], "tript_token", ETOKEN_TYPE::DEVACTION);
        $gameID = $_POST["game"];
        $game = Game::getById($gameID);
        if (is_null($game)) {
            header("HTTP/1.1 400 Bad Request");
            echo json_encode(["status" => 400, "message" => "Juego no encontrado"]);
            exit();
        }

        $version = $_POST["version"];
        $file = $_FILES["file"];

        $build = new Build($game->id, $version);
        if (!$build->setFile($file) || !$build->save()) {
            echo json_encode(["status" => 500, "message" => "Fallo al subir"]);
            exit();
        }

        echo json_encode(["status" => 200, "message" => "Compilación subida"]);
        exit();
    }

    public static function apiUploadBuildChunk()
    {
        FormHelper::ValidateToken($_POST["tript_token"], "tript_token", ETOKEN_TYPE::DEVACTION);
        $gameId = $_POST["game"];
        $version = $_POST["version"];
        $uploadId = $_POST["upload_id"];
        $chunkIndex = intval($_POST["chunk_index"]);
        $totalChunks = intval($_POST["total_chunks"]);

        $safeUploadId = preg_replace("/[^a-zA-Z0-9\-]/", "", $uploadId);
        $tmpDir = sys_get_temp_dir() . "/orion_uploads/" . $safeUploadId . "/";
        if (!is_dir($tmpDir)) mkdir($tmpDir, 0700, true);

        $chunkPath = $tmpDir . "chunk_" . str_pad($chunkIndex, 6, "0", STR_PAD_LEFT);
        move_uploaded_file($_FILES["file"]["tmp_name"], $chunkPath);

        if ($chunkIndex < $totalChunks - 1) {
            echo json_encode(["status" => 202, "message" => "Chunk recibido"]);
            exit();
        }

        $assembledPath = $tmpDir . "assembled.zip";
        $outHandle = fopen($assembledPath, "wb");
        for ($i = 0; $i < $totalChunks; $i++) {
            $part = $tmpDir . "chunk_" . str_pad($i, 6, "0", STR_PAD_LEFT);
            $inHandle = fopen($part, "rb");
            while (!feof($inHandle)) fwrite($outHandle, fread($inHandle, 8192));
            fclose($inHandle);
        }
        fclose($outHandle);

        $build = new Build($gameId, $version);
        $fakeFile = ["type" => "application/zip", "tmp_name" => $assembledPath];
        if ($build->setFile($fakeFile) && $build->save()) {
            echo json_encode(["status" => 200, "message" => "Compilación subida"]);
        } else {
            echo json_encode(["status" => 500, "message" => "Fallo al subir"]);
        }
        self::deleteDir($tmpDir);
        exit();
    }

    private static function deleteDir($dir) {
        if (!is_dir($dir)) return;
        $files = glob($dir . '*', GLOB_MARK);
        foreach ($files as $file) is_dir($file) ? self::deleteDir($file) : unlink($file);
        rmdir($dir);
    }

    public static function apiUpdateGamePublic()
    {
        FormHelper::ValidateToken($_POST["tript_token"], "tript_token", ETOKEN_TYPE::DEVACTION);
        $game = Game::getById($_POST["game"]);
        $isPublic = $_POST["isPublic"] == "true";
        $game->is_public = $isPublic;
        $game->save();
        echo json_encode(["status" => 200, "message" => "Visibilidad actualizada", "newStatus" => $isPublic ? "public" : "hidden"]);
        exit();
    }

    public static function apiCreateAchievement()
    {
        FormHelper::ValidateToken($_POST["tript_token"], "tript_token", ETOKEN_TYPE::DEVACTION);
        $game = Game::getById($_POST["game"]);
        $name = $_POST["name"];
        $desc = $_POST["description"];
        $type = EACHIEVEMENT_TYPE::tryFrom($_POST["type"]);
        $stat = $_POST["stat"] ?? null;
        $icon = $_FILES["icon"];

        $iconPath = Tript::encryptString("orionach_" . count($game->getAchievements()));
        S3Helper::upload(EBUCKET_LOCATION::GAME_ACHIEVEMENT, $iconPath, null, $icon["type"], $icon["tmp_name"]);

        $lockedIconPath = null;
        if (isset($_FILES["lockedIcon"])) {
            $lockedIconPath = Tript::encryptString("orionach_" . count($game->getAchievements()) . "_lock");
            S3Helper::upload(EBUCKET_LOCATION::GAME_ACHIEVEMENT, $lockedIconPath, null, $_FILES["lockedIcon"]["type"], $_FILES["lockedIcon"]["tmp_name"]);
        }

        $game->addAchievement($name, $desc, $iconPath, $lockedIconPath, false, $type, $stat, 0);
        echo json_encode(["status" => 201, "message" => "Logro creado"]);
        exit();
    }

    public static function apiEditAchievement()
    {
        FormHelper::ValidateToken($_POST["tript_token"], "tript_token", ETOKEN_TYPE::DEVACTION);
        $achievement = Achievement::getById($_POST["game"], $_POST["achievement"]);
        $achievement->name = $_POST["name"];
        $achievement->description = $_POST["description"];
        $achievement->type = EACHIEVEMENT_TYPE::tryFrom($_POST["type"]);
        $achievement->stat_id = $_POST["stat"] ?? null;

        if (isset($_FILES["icon"])) S3Helper::upload(EBUCKET_LOCATION::GAME_ACHIEVEMENT, $achievement->icon, null, $_FILES["icon"]["type"], $_FILES["icon"]["tmp_name"]);
        if (isset($_FILES["lockedIcon"])) {
            if (!$achievement->lockedIcon) $achievement->lockedIcon = Tript::encryptString("orionach_" . $_POST["achievement"] . "_lock");
            S3Helper::upload(EBUCKET_LOCATION::GAME_ACHIEVEMENT, $achievement->lockedIcon, null, $_FILES["lockedIcon"]["type"], $_FILES["lockedIcon"]["tmp_name"]);
        }
        $achievement->save();
        echo json_encode(["status" => 201, "message" => "Logro editado"]);
        exit();
    }

    public static function apiDeleteAchievement($gameId, $id)
    {
        FormHelper::ValidateToken($_GET["tript_token"] ?? "", "tript_token", ETOKEN_TYPE::DEVACTION);
        $achievement = Achievement::getById($gameId, $id);
        if ($achievement) $achievement->delete();
        echo json_encode(["status" => 200, "message" => "Logro eliminado"]);
        exit();
    }

    public static function apiCreateNews()
    {
        FormHelper::ValidateToken($_POST["tript_token"], "tript_token", ETOKEN_TYPE::DEVACTION);
        $user = User::getById($_SESSION["user"]["id"]);
        $news = new Post($_POST["title"], $_POST["body"], true, EPOST_TYPE::GAME_NEWS, $_POST["game"], $user->id);
        $news->save();
        $gn = new GameNews($news->id, intval($_POST["category"]));
        $gn->save();
        echo json_encode(["status" => 201, "message" => "Noticia creada"]);
        exit();
    }

    public static function apiEditNews()
    {
        FormHelper::ValidateToken($_POST["tript_token"], "tript_token", ETOKEN_TYPE::DEVACTION);
        $news = Post::getById($_POST["new"]);
        $news->title = $_POST["title"];
        $news->body = $_POST["body"];
        $news->save();
        $gameNews = GameNews::getByPostId($news->id);
        $gameNews->category_id = intval($_POST["category"]);
        $gameNews->save();
        echo json_encode(["status" => 200, "message" => "Noticia editada"]);
        exit();
    }

    public static function apiDeleteNews($gameId, $id)
    {
        FormHelper::ValidateToken($_GET["tript_token"] ?? "", "tript_token", ETOKEN_TYPE::DEVACTION);
        $news = Post::getById($id);
        if ($news && $news->game_id == $gameId) $news->delete();
        echo json_encode(["status" => 200, "message" => "Noticia eliminada"]);
        exit();
    }
}



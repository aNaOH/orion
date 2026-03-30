<?php

$router->get("/register", function () {
    if (isset($_SESSION["user"])) {
        header("location: /");
    }
    include "views/auth/register.php";
});

$router->get("/login", function () {
    if (isset($_SESSION["user"])) {
        header("location: /");
    }
    include "views/auth/login.php";
});

$router->get("/logout", function () {
    if (isset($_SESSION["user"])) {
        session_destroy();
    }

    $location = "/";

    if (isset($_GET["to"])) {
        $location = $_GET["to"] . "?from=logout";
    }

    header("location: " . $location);
});

$router->get("/profile", function () {
    if (!isset($_SESSION["user"])) {
        header("location: /");
    }

    $user = User::getById($_SESSION["user"]["id"]);

    if (!isset($user)) {
        header("location: /logout?to=login");
    }

    $GLOBALS["user"] = $user;
    $GLOBALS["is_self"] = true;

    include "views/auth/profile.php";
});

$router->get("/profile/friends", function () {
    FriendController::friendsList();
});

$router->get("/library", function () {
    if (!isset($_SESSION["user"])) {
        header("location: /");
    }

    $user = User::getById($_SESSION["user"]["id"]);

    if (!isset($user)) {
        header("location: /logout?to=login");
    }

    include "views/auth/library.php";
});

$router->get("/library/{gameid}/{version}", function ($gameid, $version) use (
    $router,
) {
    if (!isset($_SESSION["user"])) {
        header("location: /");
    }

    $user = User::getById($_SESSION["user"]["id"]);

    if (!isset($user)) {
        header("location: /logout?to=login");
    }

    $game = Game::getById($gameid);
    if (is_null($game)) {
        $router->trigger404();
    }

    if (!$user->hasAdquiredGame($game)) {
        $router->trigger404();
    }

    if ($version == "latest") {
        $build = $game->getLatestBuild();
    } else {
        $build = $game->getBuildVersion($version);
    }

    if (is_null($build)) {
        $router->trigger404();
    }

    $file = $build->getFile();
    if (is_null($file)) {
        $router->trigger404();
    }

    header("Content-Type: " . $file["type"]);
    header(
        'Content-Disposition: attachment; filename="' .
            str_replace(" ", "_", $game->title) .
            "-ver-" .
            $version .
            '.zip"',
    );
    header("Expires: 0");
    header("Cache-Control: must-revalidate");
    header("Pragma: public");

    echo $file["body"];
});

$router->get("/profile/edit", function () {
    if (!isset($_SESSION["user"])) {
        header("location: /");
    }

    $user = User::getById($_SESSION["user"]["id"]);

    if (!isset($user)) {
        header("location: /logout");
    }

    $GLOBALS["user"] = $user;

    include "views/auth/profileEdit.php";
});

$router->get("/profile/(\d+)", function ($userId) use ($router) {
    if (isset($_SESSION["user"])) {
        if ($_SESSION["user"]["id"] == $userId) {
            header("location: /profile");
        }
    }

    $user = User::getById($userId);

    if (!isset($user)) {
        $router->trigger404();
        exit();
    }

    $GLOBALS["user"] = $user;

    include "views/auth/profile.php";
});

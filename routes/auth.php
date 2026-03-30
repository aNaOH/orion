<?php

$router->get("/register", function () {
    if (isset($_SESSION["user"])) {
        header("location: /");
        exit();
    }
    ViewController::renderFromController('auth/register', ['title' => 'Unirse a Orion']);
});

$router->get("/login", function () {
    if (isset($_SESSION["user"])) {
        header("location: /");
        exit();
    }
    ViewController::renderFromController('auth/login', ['title' => 'Entrar a Orion']);
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
        header("location: /login");
        exit();
    }

    $user = User::getById($_SESSION["user"]["id"]);

    if (!isset($user)) {
        header("location: /logout?to=login");
        exit();
    }

    ViewController::renderFromController('auth/profile', [
        'target_user' => $user,
        'is_self' => true
    ]);
});

$router->get("/profile/friends", function () {
    FriendController::friendsList();
});

$router->get("/library", function () {
    if (!isset($_SESSION["user"])) {
        header("location: /login");
        exit();
    }

    $user = User::getById($_SESSION["user"]["id"]);

    if (!isset($user)) {
        header("location: /logout?to=login");
        exit();
    }

    ViewController::renderFromController('auth/library', [
        'title' => 'Tu biblioteca en Orion'
    ]);
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
        header("location: /login");
        exit();
    }

    $user = User::getById($_SESSION["user"]["id"]);

    if (!isset($user)) {
        header("location: /logout");
        exit();
    }

    ViewController::renderFromController('auth/profileEdit', [
        'title' => 'Editar perfil de Orion'
    ]);
});

$router->get("/profile/(\d+)", function ($userId) use ($router) {
    if (isset($_SESSION["user"])) {
        if ($_SESSION["user"]["id"] == $userId) {
            header("location: /profile");
            exit();
        }
    }

    $targetUser = User::getById($userId);

    if (!isset($targetUser)) {
        $router->trigger404();
        exit();
    }

    $data = [
        'target_user' => $targetUser,
        'is_self' => false,
        'has_blocked' => false,
        'is_blocked_by' => false,
        'is_friends' => false,
        'has_friend_request' => false,
        'friend_request_pending' => false
    ];

    if (isset($_SESSION["user"])) {
        $currentUser = User::getById($_SESSION["user"]["id"]);
        if ($currentUser) {
            $data['has_blocked'] = $currentUser->hasBlocked($targetUser);
            $data['is_blocked_by'] = $currentUser->isBlockedBy($targetUser);
            $data['is_friends'] = $currentUser->isFriendWith($targetUser);
            $data['has_friend_request'] = $currentUser->hasPendingFriendRequestFrom($targetUser);
            $data['friend_request_pending'] = $currentUser->isFriendRequestPending($targetUser);
        }
    }

    ViewController::renderFromController('auth/profile', $data);
});

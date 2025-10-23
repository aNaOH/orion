<?php

$router->before("GET|POST", "/admin(/.*)?", function () {
    if (isset($_SESSION["user"])) {
        $user = User::getById($_SESSION["user"]["id"]);
        if ($user) {
            if ($user->role != EUSER_TYPE::ADMIN) {
                header("location: /");
                exit();
            }
        } else {
            header("location: /");
            exit();
        }
    } else {
        header("location: /");
        exit();
    }
});

$router->before("GET|POST", "/dev/panel(/.*)?", function () {
    if (isset($_SESSION["user"])) {
        $user = User::getById($_SESSION["user"]["id"]);
        if ($user) {
            if (is_null($user->getDeveloperInfo())) {
                header("location: /");
                exit();
            }
        } else {
            header("location: /");
            exit();
        }
    } else {
        header("location: /");
        exit();
    }
});

$router->before("GET|POST", "/stripe(/.*)?", function () {
    if (isset($_SESSION["user"])) {
        $user = User::getById($_SESSION["user"]["id"]);
        if (is_null($user)) {
            header("location: /login");
            exit();
        }
    } else {
        header("location: /login");
        exit();
    }
});

$router->before("GET|POST", "/api(/.*)?", function () {
    header("Content-Type: application/json"); //Add JSON Header to all API routes
});

$router->before("GET|POST", "/api/dev/game(/.*)?", function () use ($router) {
    if (isset($_SESSION["user"])) {
        $user = User::getById($_SESSION["user"]["id"]);
        if (is_null($user)) {
            $router->trigger404();
        }

        if (is_null($user->getDeveloperInfo())) {
            $router->trigger404();
        }

        if (isset($_POST["game"])) {
            $game = Game::getById($_POST["game"] ?? -1);
            if (is_null($game)) {
                $router->trigger404();
            }

            if ($game->getDeveloper() != $user->getDeveloperInfo()) {
                $router->trigger404();
            }
        }
    } else {
        header("location: /dev/panel/games");
        exit();
    }
});

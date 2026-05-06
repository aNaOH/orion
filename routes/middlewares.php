<?php

require_once "models/User.php";
require_once "controllers/UserController.php";

$router->before("GET|POST", "/.*", function () {
    $requestPath = parse_url($_SERVER["REQUEST_URI"] ?? "", PHP_URL_PATH) ?? "";

    // Allow static assets
    if (strpos($requestPath, "/assets/") === 0 || strpos($requestPath, "/vendor/") === 0) {
        return;
    }

    // Allowed paths for suspended users
    $allowedPaths = [
        "/suspended",
        "/support/appeal",
        "/support/appeal/api/create",
        "/legal/community-guidelines",
        "/logout",
        "/login" // Allow login to see they are suspended if they try to relog
    ];

    if (in_array($requestPath, $allowedPaths)) {
        return;
    }

    if (isset($_SESSION["user"])) {
        $user = User::getById($_SESSION["user"]["id"]);
        if ($user && $user->getActiveSuspension()) {
            header("location: /suspended");
            exit();
        }
    }
});

$router->before("GET|POST", "/admin(/.*)?", function () {
    if (isset($_SESSION["user"])) {
        $user = User::getById($_SESSION["user"]["id"]);
        if ($user) {
            UserController::ensureUserIsNotSuspended($user, false);
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
            UserController::ensureUserIsNotSuspended($user, false);
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
        UserController::ensureUserIsNotSuspended($user, false);
    } else {
        header("location: /login");
        exit();
    }
});

$router->before("GET|POST", "/api(/.*)?", function () {
    header("Content-Type: application/json"); //Add JSON Header to all API routes

    $requestPath = parse_url($_SERVER["REQUEST_URI"] ?? "", PHP_URL_PATH) ?? "";
    if ($requestPath === "/api/auth/login" || $requestPath === "/api/auth/register") {
        return;
    }

    if (isset($_SESSION["user"]) && isset($_SESSION["user"]["id"])) {
        $user = User::getById($_SESSION["user"]["id"]);
        if ($user) {
            UserController::ensureUserIsNotSuspended($user);
        }
    }
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

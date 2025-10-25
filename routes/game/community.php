<?php

require_once "controllers/GameController.php";
require_once "controllers/PostController.php";

$router->mount("/communities", function () use ($router) {
    $router->get("/", function () {
        GameController::showCommunities();
    });

    $router->get("/(\d+)/", function ($gameId) use ($router) {
        $result = GameController::openCommunity($gameId);

        if ($result === false) {
            $router->trigger404();
        }
    });

    $router->get("/(\d+)/(\w+)", function ($gameId, $type) use ($router) {
        $result = false;

        if (!isset($type)) {
            $result = GameController::openCommunity($gameId);
        }

        switch ($type) {
            case "posts":
                $result = PostController::getPosts($gameId, EPOST_TYPE::POST);
                break;

            case "gallery":
                $result = PostController::getPosts(
                    $gameId,
                    EPOST_TYPE::GALLERY,
                );
                break;

            case "guides":
                $result = PostController::getPosts($gameId, EPOST_TYPE::GUIDE);
                break;

            case "news":
                $result = PostController::getPosts(
                    $gameId,
                    EPOST_TYPE::GAME_NEWS,
                );
                break;
        }

        if ($result === false) {
            $router->trigger404();
        }
    });

    $router->get("/(\d+)/(\w+)/(\d+)", function ($gameId, $type, $postId) use (
        $router,
    ) {
        $result = false;

        switch ($type) {
            case "posts":
                $result = PostController::getPost(
                    $gameId,
                    EPOST_TYPE::POST,
                    $postId,
                );
                break;

            case "gallery":
                $result = PostController::getPost(
                    $gameId,
                    EPOST_TYPE::GALLERY,
                    $postId,
                );
                break;

            case "guides":
                $result = PostController::getPost(
                    $gameId,
                    EPOST_TYPE::GUIDE,
                    $postId,
                );
                break;

            case "news":
                $result = PostController::getPost(
                    $gameId,
                    EPOST_TYPE::GAME_NEWS,
                    $postId,
                );
                break;
        }

        if ($result === false) {
            $router->trigger404();
        }
    });

    $router->get("/(\d+)/(\w+)/create", function ($gameId, $type) use (
        $router,
    ) {
        if (isset($_SESSION["user"])) {
            if (is_null(User::getById($_SESSION["user"]["id"]))) {
                $router->trigger404();
                exit();
            }
        } else {
            $router->trigger404();
            exit();
        }

        $result = false;

        switch ($type) {
            case "posts":
                $result = PostController::createPost($gameId, EPOST_TYPE::POST);
                break;

            case "gallery":
                $result = PostController::createPost(
                    $gameId,
                    EPOST_TYPE::GALLERY,
                );
                break;

            case "guides":
                $result = PostController::createPost(
                    $gameId,
                    EPOST_TYPE::GUIDE,
                );
                break;
        }

        if ($result === false) {
            $router->trigger404();
        }
    });
});

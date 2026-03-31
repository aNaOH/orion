<?php

require_once "controllers/GameController.php";
require_once "controllers/PostController.php";

class CommunityController
{
    public static function showHub()
    {
        GameController::showCommunities();
    }

    public static function showDashboard($gameId)
    {
        global $router;
        $result = GameController::openCommunity($gameId);
        if ($result === false) {
            $router->trigger404();
            exit();
        }
    }

    public static function showList($gameId, $type)
    {
        global $router;
        $result = false;

        switch ($type) {
            case "posts":
                $result = PostController::getPosts($gameId, EPOST_TYPE::POST);
                break;
            case "gallery":
                $result = PostController::getPosts($gameId, EPOST_TYPE::GALLERY);
                break;
            case "guides":
                $result = PostController::getPosts($gameId, EPOST_TYPE::GUIDE);
                break;
            case "news":
                $result = PostController::getPosts($gameId, EPOST_TYPE::GAME_NEWS);
                break;
            default:
                $result = GameController::openCommunity($gameId);
                break;
        }

        if ($result === false) {
            $router->trigger404();
            exit();
        }
    }

    public static function showPost($gameId, $type, $postId)
    {
        global $router;
        $result = false;

        switch ($type) {
            case "posts":
                $result = PostController::getPost($gameId, EPOST_TYPE::POST, $postId);
                break;
            case "gallery":
                $result = PostController::getPost($gameId, EPOST_TYPE::GALLERY, $postId);
                break;
            case "guides":
                $result = PostController::getPost($gameId, EPOST_TYPE::GUIDE, $postId);
                break;
            case "news":
                $result = PostController::getPost($gameId, EPOST_TYPE::GAME_NEWS, $postId);
                break;
        }

        if ($result === false) {
            $router->trigger404();
            exit();
        }
    }

    public static function showCreate($gameId, $type)
    {
        global $router;
        if (!isset($_SESSION["user"]) || is_null(User::getById($_SESSION["user"]["id"]))) {
            $router->trigger404();
            exit();
        }

        $result = PostController::showCreateView($gameId, $type);

        if ($result === false) {
            $router->trigger404();
            exit();
        }
    }
}


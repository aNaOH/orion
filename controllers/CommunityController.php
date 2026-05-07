<?php

require_once "controllers/GameController.php";
require_once "controllers/PostController.php";
require_once "helpers/Recommender.php";

class CommunityController
{
    public static function showHub()
    {
        $search = $_GET['search'] ?? '';
        $genre = $_GET['genre'] ?? 'all';
        $features = isset($_GET['features']) ? explode(',', $_GET['features']) : [];
        $page = $_GET['page'] ?? 1;

        $explore = $_GET['explore'] ?? 'false';

        // Si no hay búsqueda ni filtros, y no se ha pedido "explorar", mostramos la nueva Landing Page
        if (empty($search) && $genre === 'all' && empty($features) && $page == 1 && $explore === 'false') {
            $trendingCommunities = Game::getPopularCommunities(4);
            $recentPosts = Post::getLatest(8);
            
            // Recomendaciones personalizadas
            $recommendations = [];
            if (isset($_SESSION['user'])) {
                $user = User::getById($_SESSION['user']['id']);
                if ($user) {
                    $recommendations = Recommender::getRecommendations($user, false, 4);
                }
            } else {
                $recommendations = Game::pickRandom(4);
            }
            
            ViewController::render('community/landing', [
                'trendingCommunities' => $trendingCommunities,
                'recentPosts' => $recentPosts,
                'recommendations' => $recommendations,
                'genres' => GameGenre::getAll(),
                'features' => GameFeature::getAll()
            ]);
            return;
        }

        GameController::showCommunities($search, $genre, $features, $page);
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


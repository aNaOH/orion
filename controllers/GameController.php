<?php

require_once "models/Game.php";
require_once "models/Developer.php";
require_once "models/GameFeature.php";
require_once "models/GameGenre.php";
require_once "helpers/Recommender.php";

class GameController
{
    //Esta función existe con el proposito de tener una alternativa para añadir juegos mientras la tienda y los desarrolladores no estén implementados
    public static function addGameQuick(string $title)
    {
        $game = new Game(
            $title,
            null,
            null,
            null,
            null,
            null,
            false,
            true,
            null,
            1,
        );
        $game->save();

        header("HTTP/1.1 200 OK");
        $response["status"] = 200;
        $response["message"] = "Juego creado ( ID: " . strval($game->id) . " )";

        echo json_encode($response);
        exit();
    }

    public static function newGame(
        string $title,
        string $shortDescription,
        bool $asEditor,
        int $developerAccountId,
        ?string $developerName,
        int $genreId = 0,
    ) {
        FormHelper::ValidateRequiredField($title, "title");
        FormHelper::ValidateRequiredField(
            $shortDescription,
            "shortDescription",
        );

        if ($asEditor) {
            FormHelper::ValidateRequiredField($developerName, "developerName");
        }

        $game = new Game(
            $title,
            $shortDescription,
            null,
            null,
            null,
            null,
            $asEditor,
            false,
            $developerName,
            $developerAccountId,
            $genreId,
        );

        $game->save();

        header("HTTP/1.1 200 OK");
        $response["status"] = 200;
        $response["message"] = "Juego creado ( ID: " . strval($game->id) . " )";

        echo json_encode($response);
        exit();
    }

    public static function showCommunities(
        string $query = "",
        string $genre = "",
        array $features = [],
        int $page = 1,
    ) {
        $totalPages = 0;
        $games = Game::search(
            $query,
            $genre,
            $features,
            $page,
            $totalPages,
        );

        ViewController::render('community/hub', [
            'searchQuery' => $query,
            'games' => $games,
            'totalPages' => $totalPages,
            'page' => $page,
            'filteredGender' => $genre,
            'filteredFeatures' => $features,
            'genres' => GameGenre::getAll(),
            'features' => GameFeature::getAll()
        ]);
    }

    public static function showStore()
    {
        $genres = GameGenre::getAll();
        $features = GameFeature::getAll();
        $randomGames = Game::pickRandom(12);
        
        $recommended = [];
        if (isset($_SESSION["user"])) {
            $user = User::getById($_SESSION["user"]["id"]);
            $recommended = Recommender::getRecommendations($user);
        }

        ViewController::render('store/hub', [
            'genres' => $genres,
            'features' => $features,
            'randomGames' => $randomGames,
            'recommended' => $recommended,
            'searchQuery' => '',
            'filteredGender' => 'all',
            'filteredFeatures' => []
        ]);
    }

    public static function showSearch(
        string $query,
        string $genre,
        array $features,
        int $page = 1,
    ) {
        $totalPages = 0;
        $games = Game::search(
            $query,
            $genre,
            $features,
            $page,
            $totalPages,
        );

        ViewController::render('store/search', [
            'searchQuery' => $query,
            'games' => $games,
            'totalPages' => $totalPages,
            'page' => $page,
            'filteredGender' => $genre,
            'filteredFeatures' => $features,
            'genres' => GameGenre::getAll(),
            'features' => GameFeature::getAll()
        ]);
    }

    public static function openCommunity($gameId)
    {
        $game = Game::getById($gameId);

        if (is_null($game)) {
            return false;
        }

        ViewController::render('community/dashboard', [
            'game' => $game
        ]);

        return true;
    }

    public static function openGame($gameId)
    {
        $game = Game::getById($gameId);

        if (is_null($game)) {
            return false;
        }

        $news = Post::getAllByTypeAndGame(
            EPOST_TYPE::GAME_NEWS,
            $game->id,
        );

        $hasGame = false;
        if (isset($_SESSION["user"])) {
            $user = User::getById($_SESSION["user"]["id"]);
            $hasGame = $user->hasAdquiredGame($game);
        }

        ViewController::render('store/game', [
            'game' => $game,
            'news' => $news,
            'hasGame' => $hasGame,
            'requirements' => method_exists($game, 'getRequirements') ? $game->getRequirements() : []
        ]);

        return true;
    }

    public static function apiGetAchievements()
    {
        $gameId = $_POST["game"];
        $achievements = Achievement::getAllByGame($gameId);

        if (count($achievements) == 0) {
            header("HTTP/1.1 400 Bad Request");
            echo json_encode(["status" => 400, "message" => "No hay logros para este juego."]);
            exit();
        }

        $res = array_map(fn($a) => [
            "id" => $a->id,
            "name" => $a->name,
            "description" => $a->description,
            "unlockedIMG" => $a->icon,
            "lockedIMG" => $a->locked_icon,
            "isSecret" => $a->secret,
            "type" => $a->type->value,
            "statID" => $a->stat_id,
            "statValue" => $a->stat_value,
        ], $achievements);

        header("HTTP/1.1 200 OK");
        echo json_encode(["achievements" => $res]);
        exit();
    }
}



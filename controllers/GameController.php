<?php

require_once "models/Game.php";
require_once "models/Developer.php";
require_once "models/GameFeature.php";

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

    public static function showCommunities()
    {
        ViewController::render('community/hub', [
            'games' => Game::all()
        ]);
    }

    public static function showStore()
    {
        include "views/store/hub.php";
    }

    public static function showSearch(
        string $query,
        string $genre,
        array $features,
        int $page = 1,
    ) {
        $totalPages = 0;
        $GLOBALS["searchQuery"] = $query;
        $GLOBALS["games"] = Game::search(
            $query,
            $genre,
            $features,
            $page,
            $totalPages,
        );
        $GLOBALS["totalPages"] = $totalPages;
        $GLOBALS["filteredGender"] = $genre;
        $GLOBALS["filteredFeatures"] = $features;

        include "views/store/search.php";
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

        $GLOBALS["game"] = $game;
        $GLOBALS["news"] = Post::getAllByTypeAndGame(
            EPOST_TYPE::GAME_NEWS,
            $game->id,
        );

        include "views/store/index.php";
    }
}

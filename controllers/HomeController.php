<?php

require_once 'models/Game.php';
require_once 'models/User.php';

class HomeController {

    //Esta función existe con el proposito de tener una alternativa para añadir juegos mientras la tienda y los desarrolladores no estén implementados
    public static function do(){

        $games = Game::pickRandom(10);
        $users = User::getCount();

        $gameList = [];

        foreach ($games as $game) {
            $entry = [];

            $entry['id'] = $game->id;
            $entry['title'] = $game->title;

            $gameList[] = $entry;
        }

        header('HTTP/1.1 200 OK');
        $response['showcaseGames'] = $gameList;
        $response['users'] = $users;

        echo json_encode($response);
        exit();
    }

}
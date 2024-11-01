<?php

require_once 'models/Game.php';
require_once 'models/Developer.php';

class GameController {

    //Esta función existe con el proposito de tener una alternativa para añadir juegos mientras la tienda y los desarrolladores no estén implementados
    public static function addGameQuick(string $title){
        $game = new Game($title, null, null, null, null, null, null, null, 1);
        $game->save();

        header('HTTP/1.1 200 OK');
        $response['status'] = 200;
        $response['message'] = "Juego creado ( ID: ".strval($game->id)." )";

        echo json_encode($response);
        exit();
    }

    public static function showCommunities(){
        include('views/community/hub.php');
    }

    public static function openCommunity($gameId){
        $game = Game::getById($gameId);

        if(is_null($game)) return false;

        include('/views/community/index.php');
    }

}
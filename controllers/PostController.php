<?php

require_once 'models/Game.php';
require_once 'models/Post.php';

class GameController {

    //Esta función existe con el proposito de tener una alternativa para añadir juegos mientras la tienda y los desarrolladores no estén implementados
    public static function addPost(int $gameId, EPOST_TYPE $type, array $data){
        $game = Game::getById($gameId);

        if(is_null($game)) return false;

        
    }

    public static function getPosts(int $gameId, EPOST_TYPE $type, array $data){
        $game = Game::getById($gameId);

        if(is_null($game)) return false;

        $GLOBALS['game'] = $game;

        include('views/community/index.php');
    }

}
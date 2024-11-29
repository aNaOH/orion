<?php

require_once 'models/Game.php';

class HomeController {

    //Esta función existe con el proposito de tener una alternativa para añadir juegos mientras la tienda y los desarrolladores no estén implementados
    public static function do(){

        header('HTTP/1.1 200 OK');
        $response['showcaseGames'] = Game::pickRandom(10);
        $response['users'] = 0;

        echo json_encode($response);
        exit();
    }

}
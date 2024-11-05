<?php

require_once 'controllers/GameController.php';
require_once 'controllers/PostController.php';


$router->mount('/communities', function() use ($router) {

    $router->get('/', function(){
        GameController::showCommunities();
    });

    $router->get("/(\d+)/", function($gameId) use ($router){

        $result = GameController::openCommunity($gameId);

        if($result === false) $router->trigger404();
    });

    $router->get("/(\d+)/(\w+)", function($gameId, $type) use ($router){

        $result = false;

        if(!isset($type)){
            $result = GameController::openCommunity($gameId);
        }

        switch ($type) {
            case 'posts':
                $result = PostController::getPosts($gameId, EPOST_TYPE::POST);
                break;
            
            case 'gallery':
                $result = PostController::getPosts($gameId, EPOST_TYPE::GALLERY);
                break;

            case 'guides':
                $result = PostController::getPosts($gameId, EPOST_TYPE::GUIDE);
                break;
        }

        if($result === false) $router->trigger404();
    });

});
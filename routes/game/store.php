<?php

require_once 'controllers/GameController.php';
require_once 'controllers/PostController.php';


$router->mount('/store', function() use ($router) {

    $router->get('/', function(){
        GameController::showStore();
    });

    $router->get("/(\d+)/", function($gameId) use ($router){

        $result = GameController::openGame($gameId);

        if($result === false) $router->trigger404();
    });

});
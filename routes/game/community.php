<?php

require_once 'controllers/GameController.php';

$router->mount('/communities', function() use ($router) {

    $router->get('/', function(){
        GameController::showCommunities();
    });

    $router->get("/{gameId}", function($gameId) use ($router){
        $result = GameController::openCommunity($gameId);
        if($result === false) $router->trigger404();
    });

});
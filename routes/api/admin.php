<?php

require_once 'controllers/GameController.php';

$router->mount('/admin', function() use ($router) {

    $router->post('/quickgame', function(){
        $title = $_POST['title'];
        GameController::addGameQuick($title);
    });

});
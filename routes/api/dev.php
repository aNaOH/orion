<?php

require_once 'controllers/HomeController.php';
require_once 'controllers/GameController.php';

$router->mount('/dev', function() use ($router) {

    $router->get('/', function(){
        HomeController::devDo();
    });

    $router->post('/game', function(){
        $title = $_POST['title'];
        $shortDescription = $_POST['shortDescription'];
        $asEditor = $_POST['asEditor'] == 'false' ? false : true;
        $developerName = $_POST['developerName'];

        GameController::newGame($title, $shortDescription, $asEditor, $developerName);
    });

});
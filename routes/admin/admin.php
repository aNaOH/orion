<?php

$router->mount('/admin', function() use ($router) {

    $router->get('/', function(){
        include('views/admin/home.php');
    });

    $router->get('/quickgame', function(){
        include('views/admin/quickgame.php');
    });

    $router->get('/guidetype', function(){
        include('views/admin/guidetype.php');
    });

});


$router->set404('/admin(/.*)?', function() {
    header('HTTP/1.1 404 Not Found');
    include('views/admin/404.php');
});
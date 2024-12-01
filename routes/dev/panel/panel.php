<?php

$router->mount('/dev/panel', function() use ($router) {

    $router->get('/', function(){
        include('views/dev/panel/home.php');
    });

});

$router->set404('/dev/panel(/.*)?', function() {
    header('HTTP/1.1 404 Not Found');
    include('views/dev/panel/404.php');
});
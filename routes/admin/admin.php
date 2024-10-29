<?php

//API
$router->mount('/admin', function() use ($router) {

    $router->get('/', function(){
        include('views/admin/home.php');
    });

});


$router->set404('/admin(/.*)?', function() {
    header('HTTP/1.1 404 Not Found');
    include('views/admin/404.php');
});
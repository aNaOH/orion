<?php

$router->mount('/panel', function() use ($router) {

    $router->get('/', function(){
        include('views/dev/panel/home.php');
    });

    $router->get('/games', function(){
        $user = User::getById($_SESSION['user']['id']);
        $games = $user->getDeveloperInfo()->getGames();
        $GLOBALS['games'] = $games;
        include('views/dev/panel/games/index.php');
    });

});

$router->set404('/dev/panel(/.*)?', function() {
    header('HTTP/1.1 404 Not Found');
    include('views/dev/panel/404.php');
});
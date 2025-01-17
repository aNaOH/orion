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

    $router->get('/games/new', function(){
        include('views/dev/panel/games/new.php');
    });

    $router->get('/games/{gameId}/store', function($gameId) use ($router){
        $user = User::getById($_SESSION['user']['id']);
        $game = Game::getById($gameId);
        if(is_null($game) || $game->getDeveloper() != $user->getDeveloperInfo()){
            $router->trigger404();
        }
        $GLOBALS['game'] = $game;
        include('views/dev/panel/games/store/store.php');
    });

    $router->get('/games/{gameId}/community', function($gameId) use ($router){
        $user = User::getById($_SESSION['user']['id']);
        $game = Game::getById($gameId);
        if(is_null($game) || $game->getDeveloper() != $user->getDeveloperInfo()){
            $router->trigger404();
        }
        $GLOBALS['game'] = $game;
        include('views/dev/panel/games/community/community.php');
    });

    $router->get('/games/{gameId}/community/achievements', function($gameId) use ($router){
        $user = User::getById($_SESSION['user']['id']);
        $game = Game::getById($gameId);
        if(is_null($game) || $game->getDeveloper() != $user->getDeveloperInfo()){
            $router->trigger404();
        }
        $GLOBALS['game'] = $game;
        $GLOBALS['achievements'] = $game->getAchievements();

        include('views/dev/panel/games/community/achievements/index.php');
    });

    $router->get('/games/{gameId}/community/achievements/new', function($gameId) use ($router){
        $user = User::getById($_SESSION['user']['id']);
        $game = Game::getById($gameId);
        if(is_null($game) || $game->getDeveloper() != $user->getDeveloperInfo()){
            $router->trigger404();
        }
        $GLOBALS['game'] = $game;
        $GLOBALS['stats'] = $game->getStats();

        include('views/dev/panel/games/community/achievements/create.php');
    });

});

$router->set404('/dev/panel(/.*)?', function() {
    header('HTTP/1.1 404 Not Found');
    include('views/dev/panel/404.php');
});
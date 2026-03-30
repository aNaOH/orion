<?php

$router->get("/games/{gameId}/community/news", function ($gameId) use ($router) {
    $user = User::getById($_SESSION["user"]["id"]);
    $game = Game::getById($gameId);
    if (is_null($game) || $game->getDeveloper() != $user->getDeveloperInfo()) {
        $router->trigger404();
    }
    
    ViewController::render('dev/panel/games/community/news/index', [
        'game' => $game,
        'news' => Post::getAllByTypeAndGame(EPOST_TYPE::GAME_NEWS, $game->id)
    ]);
});

$router->get("/games/{gameId}/community/news/new", function ($gameId) use ($router) {
    $user = User::getById($_SESSION["user"]["id"]);
    $game = Game::getById($gameId);
    if (is_null($game) || $game->getDeveloper() != $user->getDeveloperInfo()) {
        $router->trigger404();
    }
    
    ViewController::render('dev/panel/games/community/news/create', [
        'game' => $game,
        'newscategories' => GameNewsCategory::getAll()
    ]);
});

$router->get("/games/{gameId}/community/news/{newsId}/edit", function ($gameId, $newsId) use ($router) {
    $user = User::getById($_SESSION["user"]["id"]);
    $game = Game::getById($gameId);
    if (is_null($game) || $game->getDeveloper() != $user->getDeveloperInfo()) {
        $router->trigger404();
    }
    $new = Post::getById($newsId);
    if (is_null($new) || $new->game_id != $gameId || $new->author_id != $user->id || $new->type != EPOST_TYPE::GAME_NEWS) {
        $router->trigger404();
    }

    ViewController::render('dev/panel/games/community/news/edit', [
        'game' => $game,
        'new' => $new,
        'newscategories' => GameNewsCategory::getAll()
    ]);
});

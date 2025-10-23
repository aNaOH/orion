<?php

$router->get("/games/{gameId}/community/news", function ($gameId) use (
    $router,
) {
    $user = User::getById($_SESSION["user"]["id"]);
    $game = Game::getById($gameId);
    if (is_null($game) || $game->getDeveloper() != $user->getDeveloperInfo()) {
        $router->trigger404();
    }
    $GLOBALS["game"] = $game;
    $GLOBALS["news"] = Post::getAllByTypeAndGame(
        EPOST_TYPE::GAME_NEWS,
        $game->id,
    );

    include "views/dev/panel/games/community/news/index.php";
});

$router->get("/games/{gameId}/community/news/new", function ($gameId) use (
    $router,
) {
    $user = User::getById($_SESSION["user"]["id"]);
    $game = Game::getById($gameId);
    if (is_null($game) || $game->getDeveloper() != $user->getDeveloperInfo()) {
        $router->trigger404();
    }
    $GLOBALS["game"] = $game;
    $GLOBALS["newscategories"] = GameNewsCategory::getAll();

    include "views/dev/panel/games/community/news/create.php";
});

$router->get("/games/{gameId}/community/news/{newsId}/edit", function (
    $gameId,
    $newsId,
) use ($router) {
    $user = User::getById($_SESSION["user"]["id"]);
    $game = Game::getById($gameId);
    if (is_null($game) || $game->getDeveloper() != $user->getDeveloperInfo()) {
        $router->trigger404();
    }
    $news = Post::getById($newsId);
    if (is_null($news)) {
        $router->trigger404();
    }
    if ($news->game_id != $gameId) {
        $router->trigger404();
    }
    if ($news->author_id != $user->id) {
        $router->trigger404();
    }
    if ($news->type != EPOST_TYPE::GAME_NEWS) {
        $router->trigger404();
    }

    $GLOBALS["game"] = $game;
    $GLOBALS["news"] = $news;

    include "views/dev/panel/games/community/news/edit.php";
});

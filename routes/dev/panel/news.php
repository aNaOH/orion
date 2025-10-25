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
    $new = Post::getById($newsId);
    if (is_null($new)) {
        $router->trigger404();
    }
    if ($new->game_id != $gameId) {
        $router->trigger404();
    }
    if ($new->author_id != $user->id) {
        $router->trigger404();
    }
    if ($new->type != EPOST_TYPE::GAME_NEWS) {
        $router->trigger404();
    }

    $GLOBALS["game"] = $game;
    $GLOBALS["new"] = $new;
    $GLOBALS["newscategories"] = GameNewsCategory::getAll();

    include "views/dev/panel/games/community/news/edit.php";
});

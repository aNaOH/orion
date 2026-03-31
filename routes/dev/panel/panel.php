<?php

$router->mount("/panel", function () use ($router) {
    $router->get("/", 'DevController::showPanelHome');
    $router->get("/games", 'DevController::showPanelGames');
    $router->get("/games/new", 'DevController::showPanelNewGame');
    $router->get("/games/{gameId}/store", 'DevController::showPanelGameStore');
    $router->get("/games/{gameId}/community", 'DevController::showPanelGameCommunity');
    $router->get("/games/{gameId}/community/achievements", 'DevController::showPanelGameAchievements');
    $router->get("/games/{gameId}/community/achievements/new", 'DevController::showPanelGameNewAchievement');
    $router->get("/games/{gameId}/community/achievements/{achievementId}/edit", 'DevController::showPanelGameEditAchievement');

    include_once "routes/dev/panel/news.php";
});

$router->set404("/dev/panel(/.*)?", 'DevController::handlePanel404');


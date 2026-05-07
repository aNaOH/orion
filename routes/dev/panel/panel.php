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
    $router->get("/games/{gameId}/community/leaderboards", 'DevController::showPanelGameLeaderboards');
    $router->get("/games/{gameId}/community/leaderboards/new", 'DevController::showPanelGameNewLeaderboard');
    $router->get("/games/{gameId}/community/leaderboards/{leaderboardId}/edit", 'DevController::showPanelGameEditLeaderboard');
    $router->get("/games/{gameId}/community/stats", 'DevController::showPanelGameStats');
    $router->get("/games/{gameId}/community/stats/new", 'DevController::showPanelGameNewStat');
    $router->get("/games/{gameId}/community/stats/{statId}/edit", 'DevController::showPanelGameEditStat');
    $router->get("/settings", 'DevController::showPanelSettings');
    $router->get("/payment", 'DevController::showPanelPayment');

    include_once "routes/dev/panel/news.php";
});

$router->set404("/dev/panel(/.*)?", 'DevController::handlePanel404');


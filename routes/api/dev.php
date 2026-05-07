<?php

require_once "controllers/DevController.php";

$router->mount("/dev", function () use ($router) {
    $router->get("/", 'DevController::showIndex');
    $router->post("/pay", 'DevController::apiPay');
    $router->post("/save", 'DevController::apiSaveAccount');
    $router->post("/game", 'DevController::apiCreateGame');
    $router->get("/features", 'DevController::apiGetFeatures');
    $router->post("/game/store", 'DevController::apiUpdateGameStore');
    $router->post("/game/build", 'DevController::apiUploadBuild');
    $router->post("/game/build/chunk", 'DevController::apiUploadBuildChunk');
    $router->post("/game/public", 'DevController::apiUpdateGamePublic');
    $router->post("/achievement", 'DevController::apiCreateAchievement');
    $router->post("/achievement-edit", 'DevController::apiEditAchievement');
    $router->delete("/achievement/{game}/{id}/delete/", 'DevController::apiDeleteAchievement');
    $router->post("/leaderboard", 'DevController::apiCreateLeaderboard');
    $router->post("/leaderboard-edit", 'DevController::apiEditLeaderboard');
    $router->delete("/leaderboard/{game}/{id}/delete/", 'DevController::apiDeleteLeaderboard');
    $router->post("/stat", 'DevController::apiCreateStat');
    $router->post("/stat-edit", 'DevController::apiEditStat');
    $router->delete("/stat/{game}/{id}/delete/", 'DevController::apiDeleteStat');
    $router->post("/settings", 'DevController::apiUpdateSettings');
    $router->post("/news", 'DevController::apiCreateNews');
    $router->post("/news-edit", 'DevController::apiEditNews');
    $router->delete("/news/{game}/{id}/delete/", 'DevController::apiDeleteNews');
    $router->delete("/game/{id}/delete/", 'DevController::apiDeleteGame');
});


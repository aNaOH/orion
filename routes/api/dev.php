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
    $router->post("/news", 'DevController::apiCreateNews');
    $router->post("/news-edit", 'DevController::apiEditNews');
    $router->delete("/news/{id}/delete/", 'DevController::apiDeleteNews');
});


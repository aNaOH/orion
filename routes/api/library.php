<?php

require_once "controllers/StorefrontController.php";

$router->mount("/library", function () use ($router) {
    $router->get("/", 'StorefrontController::apiGetGames');
    $router->get("/(\d+)", 'StorefrontController::apiGetGameDetails');
    $router->get("/{gameid}/download/{version}", 'StorefrontController::apiGetDownloadToken');
    $router->get("/stream/{token}", 'StorefrontController::apiStreamBuild');
});


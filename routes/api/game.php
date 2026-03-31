<?php

require_once "controllers/GameController.php";

$router->mount("/game", function () use ($router) {
    $router->post("/achievements", 'GameController::apiGetAchievements');
});


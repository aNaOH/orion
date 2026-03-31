<?php

require_once "controllers/CommunityController.php";

$router->mount("/communities", function () use ($router) {
    $router->get("/", 'CommunityController::showHub');
    $router->get("/(\d+)/", 'CommunityController::showDashboard');
    $router->get("/(\d+)/(\w+)", 'CommunityController::showList');
    $router->get("/(\d+)/(\w+)/(\d+)", 'CommunityController::showPost');
    $router->get("/(\d+)/(\w+)/create", 'CommunityController::showCreate');
});


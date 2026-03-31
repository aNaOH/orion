<?php

require_once "controllers/MediaController.php";

$router->mount("/media", function () use ($router) {
    $router->get("/profile/{uuid}", 'MediaController::showProfilePic');
    $router->get("/guidetype/{uuid}", 'MediaController::showGuideTypeIcon');
    $router->get("/gallery/{uuid}", 'MediaController::showGalleryMedia');
    $router->get("/game/{type}/{uuid}", 'MediaController::showGameMedia');
});

$router->set404("/media(/.*)?", 'MediaController::handle404');


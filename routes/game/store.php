<?php

require_once "controllers/StorefrontController.php";

$router->mount("/store", function () use ($router) {
    $router->get("/", 'StorefrontController::showHub');
    $router->get("/games", 'StorefrontController::showSearch');
    $router->get("/(\d+)/", 'StorefrontController::showGame');
    $router->get("/cart", 'StorefrontController::showCart');
});


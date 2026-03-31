<?php

require_once 'controllers/StripeController.php';

$router->mount('/stripe', function() use ($router) {
    $router->get('/dev', 'StripeController::buyDevAccount');
    $router->get('/game/{gameID}', 'StripeController::buyGame');
    $router->get('/success', 'StripeController::success');
    $router->get('/cancel', 'StripeController::cancel');
});
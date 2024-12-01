<?php

require_once 'controllers/StripeController.php';

$router->mount('/stripe', function() use ($router) {

    $router->get('/dev', function(){
        $user = User::getById($_SESSION['user']['id']);

        if(!is_null($user->getDeveloperInfo())){
            exit();
        }

        StripeController::buy($_ENV['STRIPE_DEVACCOUNT_PRICE'], $user); 
    });

    $router->get('/success', function(){
        StripeController::success();
    });

    $router->get('/cancel', function(){
        StripeController::cancel();
    });
});
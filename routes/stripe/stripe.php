<?php

use Stripe\Product;

require_once 'controllers/StripeController.php';

$router->mount('/stripe', function() use ($router) {

    $router->get('/dev', function(){
        $user = User::getById($_SESSION['user']['id']);

        if(!is_null($user->getDeveloperInfo())){
            header('location: /dev/panel');
            exit();
        }

        StripeController::buy($_ENV['STRIPE_DEVACCOUNT_PRICE'], $user, [
            'user' => $user->id,
            'developer' => $_GET['devName'] ?? ''
        ], "developer"); 
    });

    $router->get('/game/{gameID}', function($gameID) use ($router) {

        \Stripe\Stripe::setApiKey($_ENV['STRIPE_KEY']);
        
        $game = Game::getById($gameID);

        if(is_null($game)){
            $router->trigger404();
        }

        $user = User::getById($_SESSION['user']['id']);

        if($user->hasAdquiredGame($game)){
            header('location: /library#game'.strval($game->id));
            exit();
        }
        
        StripeController::buy($game, $user, [
            'user' => $user->id
        ], "game".strval($gameID)); 
    });

    $router->get('/success', function() use ($router) {
        $result = StripeController::success();
        if(!$result) {
            $router->trigger404();
        }
    });

    $router->get('/cancel', function(){
        StripeController::cancel();
    });
});
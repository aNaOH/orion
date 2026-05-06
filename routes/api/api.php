<?php

require_once "controllers/ApiController.php";
require_once "controllers/UserController.php";
require_once "controllers/CartController.php";
require_once "controllers/StorefrontController.php";

//API
$router->mount("/api", function () use ($router) {
    $router->get("/", 'ApiController::index');
    $router->post("/", 'ApiController::index');
    $router->get("/triptencrypt", 'ApiController::encrypt');
    
    $router->get("/home", function () {
        StorefrontController::apiRandomGames();
    });

    $router->post("/cart", 'CartController::add');
    $router->delete("/cart/{id}", 'CartController::remove');
    $router->post("/order", 'CartController::createOrder');
    $router->post("/order/save", 'CartController::saveOrder');

    $router->post("/auth/login", 'UserController::apiLogin');
    $router->post("/auth/register", 'UserController::apiRegister');
    $router->post("/auth/edit", 'UserController::apiEditProfile');

    include "routes/api/community.php";
    include "routes/api/admin.php";
    include "routes/api/dev.php";
    include "routes/api/library.php";
    include "routes/api/game.php";
    include "routes/api/support.php";

    $router->mount("/friends", function () use ($router) {
        $router->post("/request/(\d+)", 'UserController::apiSendFriendRequest');
        $router->post("/accept/(\d+)", 'UserController::apiAcceptFriendRequest');
        $router->post("/decline/(\d+)", 'UserController::apiDeclineFriendRequest');
        $router->post("/remove/(\d+)", 'UserController::apiRemoveFriend');
        $router->post("/block/(\d+)", 'UserController::apiBlockUser');
        $router->post("/unblock/(\d+)", 'UserController::apiUnblockUser');
    });
});

$router->set404("/api(/.*)?", 'ApiController::handle404');


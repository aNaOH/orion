<?php

require_once "controllers/AdminController.php";

$router->mount("/admin", function () use ($router) {
    $router->post("/guidetype", 'AdminController::apiCreateGuideType');
    $router->post("/guidetype/edit", 'AdminController::apiEditGuideType');
    $router->delete("/guidetype/{id}/delete/", 'AdminController::apiDeleteGuideType');
    $router->post("/gamefeature", 'AdminController::apiCreateGameFeature');
    $router->post("/tools", 'AdminController::apiRunTool');
});


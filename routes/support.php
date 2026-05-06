<?php

require_once "controllers/SupportController.php";

$router->mount("/support", function () use ($router) {
    $router->get("/report/user/{id}", 'SupportController::showReportUser');
    $router->get("/appeal", 'SupportController::showAppeal');
    $router->post("/appeal/api/create", 'SupportController::apiCreateAppeal');
});

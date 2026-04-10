<?php

require_once "controllers/SupportController.php";

$router->mount("/support", function () use ($router) {
    $router->get("/report/user/{id}", 'SupportController::showReportUser');
});

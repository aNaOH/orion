<?php

require_once "controllers/SupportController.php";

$router->mount("/support", function () use ($router) {
    $router->post("/report/user", 'SupportController::apiCreateReportUser');
});

<?php

require_once "controllers/SupportController.php";

$router->mount("/support", function () use ($router) {
    $router->get("/", 'SupportController::index');
    $router->get("/faq", 'SupportController::showFAQ');
    $router->get("/tickets", 'SupportController::showUserTickets');
    $router->get("/create", 'SupportController::showCreateTicket');
    $router->get("/safety", 'SupportController::showSafety');
    $router->post("/create/api", 'SupportController::apiCreateTicket');
    $router->get("/report/user/{id}", 'SupportController::showReportUser');
    $router->get("/appeal", 'SupportController::showAppeal');
    $router->post("/appeal/api/create", 'SupportController::apiCreateAppeal');
});

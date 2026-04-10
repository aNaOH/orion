<?php

require_once "controllers/AdminController.php";

$router->mount("/admin", function () use ($router) {
    $router->get("/", 'AdminController::showHome');
    $router->get("/guidetypes", 'AdminController::showGuideTypes');
    $router->get("/guidetypes/new", 'AdminController::showNewGuideType');
    $router->get("/guidetypes/{id}/edit/", 'AdminController::showEditGuideType');
    $router->get("/newscategories", 'AdminController::showNewsCategories');
    $router->get("/gamegenres", 'AdminController::showGameGenres');
    $router->get("/gamefeatures", 'AdminController::showGameFeatures');
    $router->get("/tools", 'AdminController::showTools');

    $router->mount("/tickets", function () use ($router) {
        require_once "controllers/AdminSupportController.php";
        $router->get("/", 'AdminSupportController::index');
        $router->get("/{id}", 'AdminSupportController::view');
        $router->post("/api/update", 'AdminSupportController::apiUpdateStatus');
    });
});

$router->set404("/admin(/.*)?", 'AdminController::handle404');


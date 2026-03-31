<?php

require_once "controllers/DevController.php";

$router->mount("/dev", function () use ($router) {
    $router->get("/", 'DevController::showIndex');

    $router->get("/(\d+)", 'DevController::showProfile');

    include "routes/dev/panel/panel.php";
});


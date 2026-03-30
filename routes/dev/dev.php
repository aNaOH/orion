<?php

require_once "models/Developer.php";

$router->mount("/dev", function () use ($router) {
    $router->get("/", function () {
        ViewController::render('dev/index', ['stripe_public_key' => $_ENV["STRIPE_PUBLIC_KEY"]]);
    });

    $router->get("/(\d+)", function ($devId) use ($router) {
        $developer = Developer::getById(intval($devId));
        if (is_null($developer)) {
            $router->trigger404();
            exit();
        }

        ViewController::render('dev/profile', ['developer' => $developer]);
    });

    include "routes/dev/panel/panel.php";
});

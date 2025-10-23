<?php

require_once "models/Developer.php";

$router->mount("/dev", function () use ($router) {
    $router->get("/", function () {
        include "views/dev/index.php";
    });

    $router->get("/(\d+)", function ($devId) use ($router) {
        $developer = Developer::getById(intval($devId));
        if (is_null($developer)) {
            $router->trigger404();
            exit();
        }

        $GLOBALS["developer"] = $developer;

        include "views/dev/profile.php";
    });

    include "routes/dev/panel/panel.php";
});

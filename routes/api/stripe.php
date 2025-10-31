<?php

$router->mount("/stripe", function () use ($router) {
    $router->post("/order", function () {
        // Your code here
    });

    $router->post("/dev", function () {
        // Your code here
    });
});

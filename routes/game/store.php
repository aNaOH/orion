<?php

require_once "controllers/GameController.php";
require_once "controllers/PostController.php";

$router->mount("/store", function () use ($router) {
    $router->get("/", function () {
        // Check if there's a search query
        if (isset($_GET["search"]) && !empty(trim($_GET["search"]))) {
            $searchQuery = $_GET["search"];
            GameController::showSearch($searchQuery);
        } else {
            GameController::showStore();
        }
    });

    $router->get("/(\d+)/", function ($gameId) use ($router) {
        $result = GameController::openGame($gameId);

        if ($result === false) {
            $router->trigger404();
        }
    });
});

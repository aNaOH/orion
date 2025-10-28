<?php

require_once "controllers/GameController.php";
require_once "controllers/PostController.php";

$router->mount("/store", function () use ($router) {
    $router->get("/", function () {
        GameController::showStore();
    });

    $router->get("/games", function () {
        $searchQuery = $_GET["search"] ?? "";
        $genre = $_GET["genre"] ?? "";
        $features =
            isset($_GET["features"]) && $_GET["features"] !== ""
                ? explode(",", $_GET["features"])
                : [];
        $page = $_GET["page"] ?? 1;
        GameController::showSearch($searchQuery, $genre, $features, $page);
    });

    $router->get("/(\d+)/", function ($gameId) use ($router) {
        $result = GameController::openGame($gameId);

        if ($result === false) {
            $router->trigger404();
        }
    });
});

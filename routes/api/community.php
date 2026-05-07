<?php

require_once "controllers/PostController.php";

$router->mount("/communities", function () use ($router) {
    $router->post("/(\d+)/(\w+)", function ($gameId, $type) {
        PostController::create(
            intval($gameId),
            $type,
            $_POST["title"] ?? "",
            $_POST["body"] ?? "",
            $_POST["token"] ?? $_POST["tript_token"] ?? "",
            $_POST["guideType"] ?? null
        );
    });

    $router->post("/comment/(\d+)", function ($postId) {
        PostController::postComment(
            intval($postId),
            $_POST["tript_token"],
            $_POST["comment"] ?? null
        );
    });

    $router->post("/vote/(\d+)", function ($postId) {
        PostController::postVote(
            intval($postId),
            $_POST["token"] ?? $_POST["tript_token"],
            $_POST["newValue"] ?? null
        );
    });
});


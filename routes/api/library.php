<?php

$router->mount("/library", function () use ($router) {
    $router->get("/", function () {
        $response = [];
        if (!isset($_SESSION["user"])) {
            //Use unauthorized status code
            http_response_code(401);
            $response["status"] = 401;
            $response["message"] = "User not logged in";
            $response["to"] = "/login";
            echo json_encode($response);
            return;
        }

        $user = User::getById($_SESSION["user"]["id"]);

        if (!isset($user)) {
            http_response_code(401);
            $response["status"] = 401;
            $response["message"] = "User not logged in";
            $response["to"] = "/logout?to=login";
            echo json_encode($response);
            return;
        }

        $games = $user->getAdquiredGames();

        foreach ($games as $game) {
            $response["data"][] = [
                "id" => $game->id,
                "title" => $game->title,
                "isDeveloper" =>
                    $game->getDeveloper()->getOwner()->id == $user->id,
            ];
        }

        echo json_encode($response);
    });

    $router->get("/(\d+)", function ($id) {
        $response = [];
        if (!isset($_SESSION["user"])) {
            //Use unauthorized status code
            http_response_code(401);
            $response["status"] = 401;
            $response["message"] = "User not logged in";
            $response["to"] = "/login";
            echo json_encode($response);
            return;
        }

        $user = User::getById($_SESSION["user"]["id"]);

        if (!isset($user)) {
            http_response_code(401);
            $response["status"] = 401;
            $response["message"] = "User not logged in";
            $response["to"] = "/logout?to=login";
            echo json_encode($response);
            return;
        }

        if (!$user->hasAdquiredGame($id)) {
            http_response_code(401);
            $response["status"] = 401;
            $response["message"] = "User has not adquired this game";
            echo json_encode($response);
            return;
        }

        $game = Game::getById($id);

        $features = $game->getFeatures();
        $featuresArray = [];

        foreach ($features as $feature) {
            $featuresArray[] = [
                "icon" => $feature->icon,
                "name" => $feature->name,
                "tint" => $feature->tint,
            ];
        }

        $achievements = $game->getAchievements();
        $achievementsArray = [];

        foreach ($achievements as $achievement) {
            $unlockedOn = "";
            $userHasAchievement = $user->hasUnlockedAchievement(
                $achievement->id,
                $unlockedOn,
            );

            $achievementsArray[] = [
                "name" => $achievement->name,
                "description" => $achievement->description,
                "icon" => $userHasAchievement
                    ? $achievement->icon
                    : $achievement->locked_icon ?? $achievement->icon,
                "unlockedOn" => $unlockedOn,
            ];
        }

        $builds = $game->getBuilds();
        $buildsArray = [];

        foreach ($builds as $build) {
            $buildsArray[] = [
                "version" => $build->version,
                "date" => $build->release_date,
                "patchNotes" => $build->patch_notes,
            ];
        }

        $response["data"] = [
            "id" => $game->id,
            "title" => $game->title,
            "features" => $featuresArray,
            "achievements" => $achievementsArray,
            "builds" => $buildsArray,
            "isDeveloper" => $game->getDeveloper()->getOwner()->id == $user->id,
        ];

        echo json_encode($response);
    });
});

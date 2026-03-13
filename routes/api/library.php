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
                $game->id,
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

    $router->get("/{gameid}/download/{version}", function ($gameid, $version) {
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

        $game = Game::getById($gameid);

        if (!$game) {
            http_response_code(404);
            $response["status"] = 404;
            $response["message"] = "Game not found";
            echo json_encode($response);
            return;
        }

        if (!$user->hasAdquiredGame($gameid)) {
            http_response_code(401);
            $response["status"] = 401;
            $response["message"] = "User has not adquired this game";
            echo json_encode($response);
            return;
        }

        if ($version == "latest") {
            $build = $game->getLatestBuild();
        } else {
            $build = $game->getBuildVersion($version);
        }

        if (!$build) {
            http_response_code(404);
            $response["status"] = 404;
            $response["message"] = "Build not found";
            echo json_encode($response);
            return;
        }

        $token = DownloadToken::createToken(
            $user->id,
            $game->id,
            $build->version,
        );

        echo json_encode([
            "token" => $token,
            "filename" =>
                str_replace(" ", "_", $game->title) .
                "-ver-" .
                $build->version .
                ".zip",
            "url" => "/api/library/stream/" . urlencode($token),
        ]);
    });

    $router->get("/stream/{token}", function ($token) use ($router) {
        $token = urldecode($token);

        if (!DownloadToken::validateToken($token)) {
            http_response_code(403);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Invalid or expired token']);
            return;
        }

        $data  = DownloadToken::getTokenData($token);
        $game  = Game::getById($data['game_id']);

        if (!$game) {
            http_response_code(404);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Game not found']);
            return;
        }

        $build = $data['version'] === 'latest'
            ? $game->getLatestBuild()
            : $game->getBuildVersion($data['version']);

        if (!$build) {
            http_response_code(404);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Build not found']);
            return;
        }

        $file = $build->getFile();

        if (!$file) {
            http_response_code(404);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'File not found in storage']);
            return;
        }

        $filename = str_replace(' ', '_', $game->title) . '-ver-' . $build->version . '.zip';

        // Cabeceras de descarga
        header('Content-Type: ' . $file['type']);
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Expires: 0');

        // Content-Length solo si conocemos el tamaño (permite barra de progreso en el frontend)
        if (!empty($file['size'])) {
            header('Content-Length: ' . $file['size']);
        }

        // Deshabilitar output buffering para que los chunks lleguen al cliente en tiempo real
        if (ob_get_level()) ob_end_clean();

        // Delegar el stream al helper (soporta tanto archivos pequeños como multipart)
        S3Helper::streamToClient($file);
    });

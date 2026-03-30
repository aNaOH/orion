<?php

$router->mount("/panel", function () use ($router) {
    $router->get("/", function () {
        $user = User::getById($_SESSION["user"]["id"]);
        $developer = $user->getDeveloperInfo();
        ViewController::render('dev/panel/home', ['developer' => $developer]);
    });

    $router->get("/games", function () {
        $user = User::getById($_SESSION["user"]["id"]);
        $games = $user->getDeveloperInfo()->getGames();
        ViewController::render('dev/panel/games/index', ['games' => $games]);
    });

    $router->get("/games/new", function () {
        ViewController::render('dev/panel/games/new');
    });

    $router->get("/games/{gameId}/store", function ($gameId) use ($router) {
        $user = User::getById($_SESSION["user"]["id"]);
        $game = Game::getById($gameId);
        if (
            is_null($game) ||
            $game->getDeveloper() != $user->getDeveloperInfo()
        ) {
            $router->trigger404();
        }

        $features = GameFeature::getAll();
        $genres = GameGenre::getAll();

        ViewController::render('dev/panel/games/store/store', [
            'game' => $game,
            'features' => $features,
            'genres' => $genres
        ]);
    });

    $router->get("/games/{gameId}/community", function ($gameId) use ($router) {
        $user = User::getById($_SESSION["user"]["id"]);
        $game = Game::getById($gameId);
        if (
            is_null($game) ||
            $game->getDeveloper() != $user->getDeveloperInfo()
        ) {
            $router->trigger404();
        }
        ViewController::render('dev/panel/games/community/community', ['game' => $game]);
    });

    $router->get("/games/{gameId}/community/achievements", function (
        $gameId,
    ) use ($router) {
        $user = User::getById($_SESSION["user"]["id"]);
        $game = Game::getById($gameId);
        if (
            is_null($game) ||
            $game->getDeveloper() != $user->getDeveloperInfo()
        ) {
            $router->trigger404();
        }
        $achievements = $game->getAchievements();

        ViewController::render('dev/panel/games/community/achievements/index', [
            'game' => $game,
            'achievements' => $achievements
        ]);
    });

    $router->get("/games/{gameId}/community/achievements/new", function (
        $gameId,
    ) use ($router) {
        $user = User::getById($_SESSION["user"]["id"]);
        $game = Game::getById($gameId);
        if (
            is_null($game) ||
            $game->getDeveloper() != $user->getDeveloperInfo()
        ) {
            $router->trigger404();
        }
        $stats = $game->getStats();

        ViewController::render('dev/panel/games/community/achievements/create', [
            'game' => $game,
            'stats' => $stats
        ]);
    });

    $router->get(
        "/games/{gameId}/community/achievements/{achievementId}/edit",
        function ($gameId, $achievementId) use ($router) {
            $user = User::getById($_SESSION["user"]["id"]);
            $game = Game::getById($gameId);
            if (
                is_null($game) ||
                $game->getDeveloper() != $user->getDeveloperInfo()
            ) {
                $router->trigger404();
            }
            $achievement = Achievement::getById($gameId, $achievementId);
            if (is_null($achievement)) {
                $router->trigger404();
            }

            $stats = $game->getStats();

            ViewController::render('dev/panel/games/community/achievements/edit', [
                'game' => $game,
                'stats' => $stats,
                'achievement' => $achievement
            ]);
        },
    );

    include_once "routes/dev/panel/news.php";
});

$router->set404("/dev/panel(/.*)?", function () {
    header("HTTP/1.1 404 Not Found");
    ViewController::render('errors/404');
});

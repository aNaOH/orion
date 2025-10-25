<?php

require_once "controllers/HomeController.php";
require_once "controllers/GameController.php";

$router->mount("/dev", function () use ($router) {
    $router->get("/", function () {
        HomeController::devDo();
    });

    $router->post("/game", function () {
        $json = json_decode(file_get_contents("php://input"), true);

        $title = $json["title"];
        $shortDescription = $json["shortDescription"];
        $asEditor = $json["asEditor"];
        $developerName = $json["developerName"];
        $genre = $json["genre"] ?? 1;

        $user = User::getById($_SESSION["user"]["id"]);
        $developer = $user->getDeveloperInfo();

        GameController::newGame(
            $title,
            $shortDescription,
            $asEditor,
            $developer->id,
            $developerName,
            $genre,
        );
    });

    $router->get("/features", function () {
        $features = GameFeature::getAll();

        $featuresArray = [];
        foreach ($features as $feature) {
            $featuresArray[] = [
                "id" => $feature->id,
                "name" => $feature->name,
                "icon" => $feature->icon,
                "tint" => $feature->tint,
            ];
        }

        $response["status"] = 200;
        $response["data"] = $featuresArray;

        echo json_encode($response);
    });

    $router->post("/game/store", function () {
        $rawData = json_decode($_POST["data"] ?? "{}", true);

        $gameID = $rawData["game"] ?? null;
        if (is_null($gameID)) {
            header("HTTP/1.1 400 Bad Request");
            echo json_encode([
                "status" => 400,
                "message" => "Falta el ID del juego",
            ]);
            exit();
        }

        $game = Game::getById($gameID);
        if (is_null($game)) {
            header("HTTP/1.1 400 Bad Request");
            echo json_encode([
                "status" => 400,
                "message" => "No existe ningún juego con ese ID. ($gameID)",
            ]);
            exit();
        }

        // Asignar valores desde $rawData
        $title = $rawData["title"] ?? "";
        $shortDescription = $rawData["shortDescription"] ?? "";
        $asEditor = $rawData["asEditor"] ?? false;
        $developerName = $rawData["developerName"] ?? "";
        $description = $rawData["description"] ?? "";
        $price = $rawData["price"] ?? 0;
        $discount = $rawData["discount"] ?? 0;
        $genre = $rawData["genre"] ?? 1;
        $features = $rawData["features"] ?? [];

        $cover = $_FILES["coverFile"] ?? null;
        $thumb = $_FILES["thumbFile"] ?? null;
        $icon = $_FILES["iconFile"] ?? null;

        $game->title = $title;
        $game->short_description = $shortDescription;
        $game->as_editor = $asEditor;
        $game->developer_name = $developerName;
        $game->description = $description;
        $game->base_price = $price;
        $game->discount = $discount / 100;
        $game->genre_id = $genre;

        $game->setFeatures($features);

        $game->save();

        if (!is_null($cover)) {
            S3Helper::upload(
                EBUCKET_LOCATION::GAME_COVER,
                $game->id,
                null,
                $cover["type"],
                $cover["tmp_name"],
            );
        }

        if (!is_null($thumb)) {
            S3Helper::upload(
                EBUCKET_LOCATION::GAME_THUMB,
                $game->id,
                null,
                $thumb["type"],
                $thumb["tmp_name"],
            );
        }

        if (!is_null($icon)) {
            S3Helper::upload(
                EBUCKET_LOCATION::GAME_ICON,
                $game->id,
                null,
                $icon["type"],
                $icon["tmp_name"],
            );
        }

        header("HTTP/1.1 200 OK");
        $response["status"] = 200;
        $response["message"] =
            "Juego editado ( ID: " . strval($game->id) . " )";

        echo json_encode($response);
        exit();
    });

    $router->post("/game/build", function () {
        $gameID = $_POST["game"];

        $game = Game::getById($gameID);
        if (is_null($game)) {
            header("HTTP/1.1 400 Bad Request");
            $response["status"] = 400;
            $response[
                "message"
            ] = "No existe ningún juego con ese ID. ($gameID)";

            echo json_encode($response);
            exit();
        }

        FormHelper::ValidateRequiredField($_POST["version"], "version");
        FormHelper::ValidateRequiredFile($_FILES["file"], "file");

        $version = $_POST["version"];
        $file = $_FILES["file"];

        $build = new Build($game->id, $version);

        if (!$build->setFile($file) || !$build->save()) {
            header("HTTP/1.1 500 Internal Server Error");
            $response["status"] = 500;
            $response["message"] =
                "Fallo al subir la compilación, prueba más tarde";
            $response["field"] = "file";

            echo json_encode($response);
            exit();
        }

        header("HTTP/1.1 200 OK");
        $response["status"] = 200;
        $response["message"] =
            "Compilación subida ( Game ID: " .
            strval($game->id) .
            " Version: " .
            $version .
            " )";

        echo json_encode($response);
        exit();
    });

    $router->post("/game/public", function () {
        $gameID = $_POST["game"];

        $game = Game::getById($gameID);
        if (is_null($game)) {
            header("HTTP/1.1 400 Bad Request");
            $response["status"] = 400;
            $response[
                "message"
            ] = "No existe ningún juego con ese ID. ($gameID)";

            echo json_encode($response);
            exit();
        }

        $isPublic = $_POST["isPublic"] == "true";
        $game->is_public = $isPublic;
        $game->save();

        header("HTTP/1.1 200 OK");
        $response["status"] = 200;
        $response["message"] =
            "Compilación subida ( Game ID: " .
            strval($game->id) .
            " ¿Publico?: " .
            $isPublic .
            " )";
        $response["newStatus"] = $isPublic ? "public" : "hidden";

        echo json_encode($response);
        exit();
    });

    $router->post("/achievement", function () {
        $gameID = $_POST["game"];
        $game = Game::getById($gameID);

        if (is_null($game)) {
            header("HTTP/1.1 400 Bad Request");
            $response["status"] = 400;
            $response["field"] = "submit";
            $response[
                "message"
            ] = "No existe ningún juego con ese ID. ($gameID)";

            echo json_encode($response);
            exit();
        }

        $name = $_POST["name"];
        $description = $_POST["description"];
        $type = EACHIEVEMENT_TYPE::tryFrom($_POST["type"]);
        if (!$type) {
            header("HTTP/1.1 400 Bad Request");
            $response["status"] = 400;
            $response["field"] = "type";
            $response["message"] = "Tipo de logro inválido.";
            echo json_encode($response);
            exit();
        }
        $stat = $_POST["stat"] ?? null;
        if ($stat < 1) {
            $stat = null;
        }

        $icon = $_FILES["icon"] ?? null;
        $lockedIcon = $_FILES["lockedIcon"] ?? null;

        if ($type == EACHIEVEMENT_TYPE::STAT && !$stat) {
            header("HTTP/1.1 400 Bad Request");
            $response["status"] = 400;
            $response["field"] = "stat";
            $response["message"] = "La estadística es obligatoria.";
            echo json_encode($response);
            exit();
        }

        if (!$icon) {
            header("HTTP/1.1 400 Bad Request");
            $response["status"] = 400;
            $response["field"] = "icon";
            $response["message"] = "El icono es obligatorio.";
            echo json_encode($response);
            exit();
        }

        $iconPath = Tript::encryptString(
            "orionach_" . strval(count($game->getAchievements())),
        );
        $lockedIconPath = null;

        S3Helper::upload(
            EBUCKET_LOCATION::GAME_ACHIEVEMENT,
            $iconPath,
            null,
            $icon["type"],
            $icon["tmp_name"],
        );
        if ($lockedIcon) {
            $lockedIconPath = Tript::encryptString(
                "orionach_" . strval(count($game->getAchievements())) . "_lock",
            );
            S3Helper::upload(
                EBUCKET_LOCATION::GAME_ACHIEVEMENT,
                $lockedIconPath,
                null,
                $lockedIcon["type"],
                $lockedIcon["tmp_name"],
            );
        }

        $done = $game->addAchievement(
            $name,
            $description,
            $iconPath,
            $lockedIconPath,
            false,
            $type,
            $stat,
            0,
        );

        if ($done) {
            header("HTTP/1.1 201 Created");
            $response["status"] = 201;
            $response["message"] = "Logro creado exitosamente.";
            echo json_encode($response);
        } else {
            header("HTTP/1.1 500 Internal Server Error");
            $response["status"] = 500;
            $response["message"] = "Error al crear el logro.";
            echo json_encode($response);
        }
        exit();
    });

    $router->post("/achievement-edit", function () {
        $gameID = $_POST["game"];
        $game = Game::getById($gameID);
        $achievementID = $_POST["achievement"];

        if (is_null($game)) {
            header("HTTP/1.1 400 Bad Request");
            $response["status"] = 400;
            $response["field"] = "submit";
            $response[
                "message"
            ] = "No existe ningún juego con ese ID. ($gameID)";

            echo json_encode($response);
            exit();
        }

        $achievement = Achievement::getById($gameID, $achievementID);

        if (is_null($achievement)) {
            header("HTTP/1.1 400 Bad Request");
            $response["status"] = 400;
            $response["field"] = "submit";
            $response[
                "message"
            ] = "No existe ningún logro con ese ID. ($achievementID)";

            echo json_encode($response);
            exit();
        }

        $name = $_POST["name"];
        $description = $_POST["description"];
        $type = EACHIEVEMENT_TYPE::tryFrom($_POST["type"]);
        if (!$type) {
            header("HTTP/1.1 400 Bad Request");
            $response["status"] = 400;
            $response["field"] = "type";
            $response["message"] = "Tipo de logro inválido.";
            echo json_encode($response);
            exit();
        }
        $stat = $_POST["stat"] ?? null;
        if ($stat < 1) {
            $stat = null;
        }

        $icon = $_FILES["icon"] ?? null;
        $lockedIcon = $_FILES["lockedIcon"] ?? null;

        if ($type == EACHIEVEMENT_TYPE::STAT && !$stat) {
            header("HTTP/1.1 400 Bad Request");
            $response["status"] = 400;
            $response["field"] = "stat";
            $response["message"] = "La estadística es obligatoria.";
            echo json_encode($response);
            exit();
        }

        if ($icon) {
            S3Helper::upload(
                EBUCKET_LOCATION::GAME_ACHIEVEMENT,
                $achievement->icon,
                null,
                $icon["type"],
                $icon["tmp_name"],
            );
        }

        $lockedIconPath = null;

        if ($lockedIcon) {
            if (!$achievement->lockedIcon) {
                $lockedIconPath = Tript::encryptString(
                    "orionach_" .
                        strval(count($game->getAchievements())) .
                        "_lock",
                );
            } else {
                $lockedIconPath = $achievement->lockedIcon;
            }

            S3Helper::upload(
                EBUCKET_LOCATION::GAME_ACHIEVEMENT,
                $lockedIconPath,
                null,
                $lockedIcon["type"],
                $lockedIcon["tmp_name"],
            );
        }

        $achievement->name = $name;
        $achievement->description = $description;
        $achievement->type = $type;
        $achievement->stat_id = $stat;

        $done = $achievement->save();

        if ($done) {
            header("HTTP/1.1 201 Created");
            $response["status"] = 201;
            $response["message"] = "Logro editado exitosamente.";
            echo json_encode($response);
        } else {
            header("HTTP/1.1 500 Internal Server Error");
            $response["status"] = 500;
            $response["message"] = "Error al editar el logro.";
            echo json_encode($response);
        }
        exit();
    });

    $router->delete("/achievement/{game}/{id}/delete/", function (
        $gameId,
        $id,
    ) {
        $achievement = Achievement::getById($gameId, $id);
        if ($achievement) {
            $achievement->delete();
            header("HTTP/1.1 200 OK");
            $response["status"] = 200;
            $response["message"] = "Tipo de guía eliminado";
            echo json_encode($response);
        } else {
            header("HTTP/1.1 404 Not Found");
            $response["status"] = 404;
            $response["message"] = "Tipo de guía no encontrado";
            echo json_encode($response);
        }
    });

    $router->post("/news", function () {
        $gameId = $_POST["game"];
        $game = Game::getById($gameId);

        if (!$game) {
            header("HTTP/1.1 404 Not Found");
            $response["status"] = 404;
            $response["message"] = "Juego no encontrado.";
            echo json_encode($response);
            exit();
        }

        $user = User::getById($_SESSION["user"]["id"]);

        if (!$user) {
            header("HTTP/1.1 401 Unauthorized");
            $response["status"] = 401;
            $response["message"] = "Usuario no autenticado.";
            echo json_encode($response);
            exit();
        }

        if ($user->getDeveloperInfo()->id != $game->developer_id) {
            header("HTTP/1.1 403 Forbidden");
            $response["status"] = 403;
            $response["message"] = "No tienes permiso para crear noticias.";
            echo json_encode($response);
            exit();
        }

        $title = $_POST["title"];
        $content = $_POST["body"];
        $category = intval($_POST["category"]);

        FormHelper::ValidateRequiredField($title, "title");
        FormHelper::ValidateRequiredField($content, "body");
        FormHelper::ValidateRequiredField($category, "category");

        if ($category < 1) {
            header("HTTP/1.1 400 Bad Request");
            $response["status"] = 400;
            $response["message"] = "La categoría no existe.";
            echo json_encode($response);
            exit();
        }

        $categoryObj = GameNewsCategory::getById($category);

        if (!$categoryObj) {
            header("HTTP/1.1 400 Bad Request");
            $response["status"] = 400;
            $response["message"] = "La categoría no existe.";
            echo json_encode($response);
            exit();
        }

        $news = new Post(
            $title,
            $content,
            true,
            EPOST_TYPE::GAME_NEWS,
            $gameId,
            $user->id,
        );

        $done = $news->save();

        if (!$done) {
            header("HTTP/1.1 500 Internal Server Error");
            $response["status"] = 500;
            $response["message"] = "Error al crear la noticia.";
            echo json_encode($response);
            exit();
        }

        $gameNews = new GameNews($news->id, $category);

        $done = $gameNews->save();

        if ($done) {
            header("HTTP/1.1 201 Created");
            $response["status"] = 201;
            $response["message"] = "Noticia creada exitosamente.";
            echo json_encode($response);
        } else {
            header("HTTP/1.1 500 Internal Server Error");
            $response["status"] = 500;
            $response["message"] = "Error al crear la noticia.";
            echo json_encode($response);
        }
        exit();
    });

    $router->post("/news-edit", function () {
        $newsId = $_POST["new"];
        $gameId = $_POST["game"];
        $title = $_POST["title"];
        $content = $_POST["body"];
        $category = intval($_POST["category"]);

        // Validar que el ID de la noticia esté presente
        if (!$newsId) {
            header("HTTP/1.1 400 Bad Request");
            $response["status"] = 400;
            $response["error"] = "ID de noticia requerido.";
            echo json_encode($response);
            exit();
        }

        // Obtener la noticia existente
        $news = Post::getById($newsId);
        if (!$news || $news->type != EPOST_TYPE::GAME_NEWS) {
            header("HTTP/1.1 404 Not Found");
            $response["status"] = 404;
            $response["error"] = "Noticia no encontrada.";
            echo json_encode($response);
            exit();
        }

        // Verificar que el juego existe
        $game = Game::getById($gameId);
        if (!$game) {
            header("HTTP/1.1 404 Not Found");
            $response["status"] = 404;
            $response["error"] = "Juego no encontrado.";
            echo json_encode($response);
            exit();
        }

        // Verificar autenticación del usuario
        $user = User::getById($_SESSION["user"]["id"]);
        if (!$user) {
            header("HTTP/1.1 401 Unauthorized");
            $response["status"] = 401;
            $response["error"] = "Usuario no autenticado.";
            echo json_encode($response);
            exit();
        }

        // Verificar permisos: el usuario debe ser el autor o el desarrollador del juego
        if (
            $news->author_id != $user->id &&
            $user->getDeveloperInfo()->id != $game->developer_id
        ) {
            header("HTTP/1.1 403 Forbidden");
            $response["status"] = 403;
            $response["error"] = "No tienes permiso para editar esta noticia.";
            echo json_encode($response);
            exit();
        }

        $gameNewInfo = $news->getPostInfo();
        if (!($gameNewInfo instanceof GameNews)) {
            header("HTTP/1.1 404 Not Found");
            $response["status"] = 404;
            $response["error"] = "Este post no es una noticia de juego.";
            echo json_encode($response);
            exit();
        }

        // Validar campos requeridos
        FormHelper::ValidateRequiredField($title, "title");
        FormHelper::ValidateRequiredField($content, "body");
        FormHelper::ValidateRequiredField($category, "category");

        if ($category < 1) {
            header("HTTP/1.1 400 Bad Request");
            $response["status"] = 400;
            $response["error"] = "La categoría no existe.";
            echo json_encode($response);
            exit();
        }

        // Actualizar los datos de la noticia
        $news->title = $title;
        $news->body = $content;
        $news->last_updated_at = new DateTime();

        $done = $news->save();

        if (!$done) {
            header("HTTP/1.1 500 Internal Server Error");
            $response["status"] = 500;
            $response["error"] = "Error al actualizar la noticia.";
            echo json_encode($response);
            exit();
        }

        if ($gameNewInfo->category_id != $category) {
            // Verificar que la categoría existe
            $categoryObj = GameNewsCategory::getById($category);
            if (!$categoryObj) {
                header("HTTP/1.1 400 Bad Request");
                $response["status"] = 400;
                $response["error"] = "La categoría no existe.";
                echo json_encode($response);
                exit();
            }

            // Actualizar la categoría de la noticia
            $gameNews = GameNews::getByPostId($news->id);
            if ($gameNews) {
                $gameNews->category_id = $category;
                $done = $gameNews->save();

                if (!$done) {
                    header("HTTP/1.1 500 Internal Server Error");
                    $response["status"] = 500;
                    $response["error"] =
                        "Error al actualizar la categoría de la noticia.";
                    echo json_encode($response);
                    exit();
                }
            } else {
                header("HTTP/1.1 500 Internal Server Error");
                $response["status"] = 500;
                $response["error"] =
                    "Error: información de categoría no encontrada.";
                echo json_encode($response);
                exit();
            }
        }

        header("HTTP/1.1 201 Created");
        $response["status"] = 201;
        $response["message"] = "Noticia actualizada exitosamente.";
        echo json_encode($response);
    });

    $router->delete("/news/{id}/delete/", function ($id) {
        $news = Post::getById($id);
        if (
            $news &&
            $news->author_id == $_SESSION["user"]["id"] &&
            $news->type == EPOST_TYPE::GAME_NEWS
        ) {
            $news->delete();
            header("HTTP/1.1 200 OK");
            $response["status"] = 200;
            $response["message"] = "Noticia eliminada";
            echo json_encode($response);
        } else {
            header("HTTP/1.1 404 Not Found");
            $response["status"] = 404;
            $response["message"] = "Noticia no encontrada";
            echo json_encode($response);
        }
    });
});

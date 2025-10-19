<?php

require_once "controllers/GameController.php";
require_once "models/GuideType.php";

$router->mount("/admin", function () use ($router) {
    $router->post("/quickgame", function () {
        $title = $_POST["title"];
        GameController::addGameQuick($title);
    });

    $router->post("/guidetype", function () {
        $type = $_POST["type"];
        $tint = $_POST["tintColor"];

        $uploadedIcon = $_FILES["icon"];

        $name = str_replace(".svg", "", $uploadedIcon["name"]);
        $name = str_replace("_", "", $name);
        $name = str_replace("-", "", $name);

        $uuid = Tript::encryptString("guidetypeicon" . $name);

        S3Helper::upload(
            EBUCKET_LOCATION::GUIDE_TYPE_ICON,
            $uuid,
            null,
            "image/svg+xml",
            $uploadedIcon["tmp_name"],
        );

        $guideType = new GuideType($uuid, $type, $tint);
        $guideType->save();

        header("HTTP/1.1 200 OK");
        $response["status"] = 200;
        $response["message"] =
            "Tipo de guía creado ( ID: " . strval($guideType->id) . " )";

        echo json_encode($response);
        exit();
    });

    $router->post("/gamefeature", function () {
        $feature = $_POST["name"];
        $tint = $_POST["tintColor"];

        $uploadedIcon = $_FILES["icon"];

        $name = str_replace(".svg", "", $uploadedIcon["name"]);
        $name = str_replace("_", "", $name);
        $name = str_replace("-", "", $name);

        $uuid = Tript::encryptString("gamefeatureicon" . $name);

        S3Helper::upload(
            EBUCKET_LOCATION::GAME_FEATURE_ICON,
            $uuid,
            null,
            "image/svg+xml",
            $uploadedIcon["tmp_name"],
        );

        $gameFeature = new GameFeature($uuid, $feature, $tint);
        $gameFeature->save();

        header("HTTP/1.1 200 OK");
        $response["status"] = 200;
        $response["message"] =
            "Característica creada ( ID: " . strval($gameFeature->id) . " )";

        echo json_encode($response);
        exit();
    });
});

<?php

require_once "controllers/ViewController.php";
require_once "models/GuideType.php";
require_once "models/GameNewsCategory.php";
require_once "models/GameGenre.php";
require_once "models/GameFeature.php";
require_once "emails/TestEmail.php";
require_once "emails/NoAssetMail.php";

class AdminController
{
    public static function showHome()
    {
        ViewController::render('admin/home');
    }

    public static function showGuideTypes()
    {
        $guidetypes = GuideType::getAll();
        ViewController::render('admin/guidetypes/index', ['guidetypes' => $guidetypes]);
    }

    public static function showNewGuideType()
    {
        ViewController::render('admin/guidetypes/new');
    }

    public static function showEditGuideType($id)
    {
        $guidetype = GuideType::getById($id);
        ViewController::render('admin/guidetypes/edit', ['guidetype' => $guidetype]);
    }

    public static function showNewsCategories()
    {
        $newscategories = GameNewsCategory::getAll();
        ViewController::render('admin/newscategories/index', ['newscategories' => $newscategories]);
    }

    public static function showGameGenres()
    {
        $gamegenres = GameGenre::getAll();
        ViewController::render('admin/gamegenres/index', ['gamegenres' => $gamegenres]);
    }

    public static function showGameFeatures()
    {
        $gamefeatures = GameFeature::getAll();
        ViewController::render('admin/gamefeatures/index', ['gamefeatures' => $gamefeatures]);
    }

    public static function showTools()
    {
        ViewController::render('admin/tools');
    }

    public static function handle404()
    {
        header("HTTP/1.1 404 Not Found");
        ViewController::render('errors/404');
    }

    public static function apiCreateGuideType()
    {
        FormHelper::ValidateToken($_POST["tript_token"], "tript_token", ETOKEN_TYPE::COMMON);
        $type = $_POST["type"];
        $tint = $_POST["tintColor"];
        $uploadedIcon = $_FILES["icon"];

        $name = str_replace([".svg", "_", "-"], "", $uploadedIcon["name"]);
        $uuid = Tript::encryptString("guidetypeicon" . $name);

        S3Helper::upload(EBUCKET_LOCATION::GUIDE_TYPE_ICON, $uuid, null, "image/svg+xml", $uploadedIcon["tmp_name"]);

        $guideType = new GuideType($uuid, $type, $tint);
        $guideType->save();

        header("HTTP/1.1 200 OK");
        echo json_encode(["status" => 200, "message" => "Tipo de guía creado ( ID: " . strval($guideType->id) . " )"]);
        exit();
    }

    public static function apiEditGuideType()
    {
        FormHelper::ValidateToken($_POST["tript_token"], "tript_token", ETOKEN_TYPE::COMMON);
        $id = $_POST["id"];
        $type = $_POST["type"];
        $tint = $_POST["tint"];

        $guideType = GuideType::getById($id);
        if ($guideType) {
            $guideType->type = $type;
            $guideType->tint = $tint;
            $guideType->save();
            header("HTTP/1.1 200 OK");
            echo json_encode(["status" => 200, "message" => "Tipo de guía actualizado"]);
        } else {
            header("HTTP/1.1 404 Not Found");
            echo json_encode(["status" => 404, "message" => "Tipo de guía no encontrado"]);
        }
        exit();
    }

    public static function apiDeleteGuideType($id)
    {
        $guideType = GuideType::getById($id);
        if ($guideType) {
            $guideType->delete();
            header("HTTP/1.1 200 OK");
            echo json_encode(["status" => 200, "message" => "Tipo de guía eliminado"]);
        } else {
            header("HTTP/1.1 404 Not Found");
            echo json_encode(["status" => 404, "message" => "Tipo de guía no encontrado"]);
        }
        exit();
    }

    public static function apiCreateGameFeature()
    {
        FormHelper::ValidateToken($_POST["tript_token"], "tript_token", ETOKEN_TYPE::COMMON);
        $feature = $_POST["name"];
        $tint = $_POST["tintColor"];
        $uploadedIcon = $_FILES["icon"];

        $name = str_replace([".svg", "_", "-"], "", $uploadedIcon["name"]);
        $uuid = Tript::encryptString("gamefeatureicon" . $name);

        S3Helper::upload(EBUCKET_LOCATION::GAME_FEATURE_ICON, $uuid, null, "image/svg+xml", $uploadedIcon["tmp_name"]);

        $gameFeature = new GameFeature($uuid, $feature, $tint);
        $gameFeature->save();

        header("HTTP/1.1 200 OK");
        echo json_encode(["status" => 200, "message" => "Característica creada ( ID: " . strval($gameFeature->id) . " )"]);
        exit();
    }

    public static function apiRunTool()
    {
        $json = json_decode(file_get_contents("php://input"), true);
        $tool = $json["tool"];

        if ($tool === "email") {
            $email = $json["email"];
            $template = $json["template"];

            FormHelper::ValidateRequiredField($email, "email");
            FormHelper::ValidateRequiredField($template, "template");
            FormHelper::ValidateEmailField($email, "email");

            try {
                switch ($template) {
                    case "testemail": $emailObj = new TestEmail($email); break;
                    case "noassetmail": $emailObj = new NoAssetMail($email); break;
                    default: throw new Exception("Invalid template");
                }
                $emailObj->send();
                header("HTTP/1.1 200 OK");
                echo json_encode(["status" => 200, "message" => "Correo enviado"]);
            } catch (Exception $e) {
                header("HTTP/1.1 400 Bad Request");
                echo json_encode(["status" => 400, "message" => $e->getMessage()]);
            }
            exit();
        }

        header("HTTP/1.1 500 Internal Server Error");
        echo json_encode(["status" => 500, "message" => "Algo salió mal"]);
        exit();
    }
}


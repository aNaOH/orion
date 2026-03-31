<?php

class ApiController
{
    public static function index()
    {
        $response = [];
        $date = new DateTime();
        header("HTTP/1.1 200 OK");
        $response["name"] = "Orion API";
        $response["author"] = "Abel";
        $response["lastModifiedDate"] = "2025-10-19";
        $response["currentSystemDate"] = $date->format("Y-m-d");
        $response["message"] = "hello!";
        $response["surprise"] =
            "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAkAAAAICAIAAACkr0LiAAAACXBIWXMAAA9hAAAPYQGoP6dpAAAAdUlEQVQImV2OQQ3DQADDvKoEQuEoDOUgtBQOwkqhFELBFPZoNU3LK4/YymPb3gDIX2S5SpLOV+cOqBBh/e4yBuRmNMnqZRPyjLW9POICBGzbszCPfR7zwleU5DimOkZCasWQ5eeXwHMEohVW7wMC1bOFJAP9AD4XPtggqnyXAAAAAElFTkSuQmCC";
        echo json_encode($response);
        exit();
    }

    public static function encrypt()
    {
        $header = $_GET["header"] ?? "";
        $name = $_GET["name"] ?? "";
        $triptText = Tript::encryptString($header . $name);
        $response = [];
        $response["result"] = $triptText;
        echo json_encode($response);
        exit();
    }

    public static function handle404()
    {
        header("HTTP/1.1 404 Not Found");
        header("Content-Type: application/json");
        $jsonArray = ["status" => "404", "status_text" => "route not defined"];
        echo json_encode($jsonArray);
        exit();
    }
}

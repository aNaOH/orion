<?php

require_once 'controllers/GameController.php';
require_once 'models/GuideType.php';


$router->mount('/admin', function() use ($router) {

    $router->post('/quickgame', function(){
        $title = $_POST['title'];
        GameController::addGameQuick($title);
    });

    $router->post('/guidetype', function(){
        $type = $_POST['type'];

        $uploadedIcon = $_FILES['icon'];

        //$guideType = new GuideType();
        //$guideType->save();

        var_dump($uploadedIcon);

        header('HTTP/1.1 200 OK');
        //$response['status'] = 200;
        //$response['message'] = "Tipo de guía creado ( ID: ".strval($guideType->id)." )";

        //echo json_encode($response);
        exit();
    });

});
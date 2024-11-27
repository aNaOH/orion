<?php

require_once 'controllers/GameController.php';
require_once 'controllers/PostController.php';


$router->mount('/communities', function() use ($router) {

    $router->post("/(\d+)/(\w+)", function($gameId, $type) use ($router){

        $result = false;

        FormHelper::ValidateToken($_POST['token'], 'tript_token', ETOKEN_TYPE::USERACTION);

        $postType = EPOST_TYPE::POST;

        switch ($type) {
            
            case 'gallery':
                $postType = EPOST_TYPE::GALLERY;
                break;

            case 'guides':
                $postType = EPOST_TYPE::GUIDE;
                break;
        }

        $result = PostController::create(intval($gameId), $postType, $_POST['title'], $_POST['body'], isset($_POST['guideType']) ? $_POST['guideType'] : null);

        if($result === false) $router->trigger404();
    });

});
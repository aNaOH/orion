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
                $_POST['body'] = "gm";
                break;

            case 'guides':
                $postType = EPOST_TYPE::GUIDE;
                break;
        }

        $result = PostController::create(intval($gameId), $postType, $_POST['title'], $_POST['body'], isset($_POST['guideType']) ? $_POST['guideType'] : null);

        if($result === false) $router->trigger404();
    });

    $router->post("/comment/(\d+)", function($postId) use ($router){

        $result = false;

        FormHelper::ValidateToken($_POST['tript_token'], 'tript_token', ETOKEN_TYPE::USERACTION);

        $body = trim($_POST['comment'] ?? "");

        if(strlen($body) == 0) $body = "El usuario no ha escrito nada...";

        $result = PostController::addComment(intval($postId), $body);

        if($result === false) {
            $router->trigger404();
            exit();
        }

        $post = Post::getById($postId);

        $type = "posts";

        switch ($post->type) {
            case EPOST_TYPE::GUIDE:
                $type = 'guides';
                break;
            
            case EPOST_TYPE::GALLERY:
                $type = 'gallery';
                break;
        }

        header('location: /communities/'.strval($post->game_id).'/'.$type.'/'.strval($post->id));
    
    });

    $router->post("/vote/(\d+)", function($postId) use ($router){
        FormHelper::ValidateToken($_POST['token'], 'tript_token', ETOKEN_TYPE::USERACTION);
        FormHelper::ValidateRequiredField($_POST['newValue'], "newValue");

        $vote = $_POST['newValue'];

        PostController::vote(intval($postId), $vote);
    });

});
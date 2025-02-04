<?php

$router->mount('/media', function() use ($router) {

    $router->get('/profile/{uuid}', function($uuid) use ($router) {

        if($uuid == "default"){
            $uuid .= ".png";
        }

        $img = S3Helper::retrieve(EBUCKET_LOCATION::PROFILE_PIC, $uuid);

        if(!isset($img)){
            $router->trigger404();
        }

        header('Content-Type: '.$img['type']); //Add JSON Header to all API routes

        echo $img['body'];
    });

    $router->get('/guidetype/{uuid}', function($uuid) use ($router) {

        $img = S3Helper::retrieve(EBUCKET_LOCATION::GUIDE_TYPE_ICON, $uuid);

        if(!isset($img)){
            $router->trigger404();
        }

        header('Content-Type: '.$img['type']); //Add JSON Header to all API routes

        echo $img['body'];
    });

    $router->get('/gallery/{uuid}', function($uuid) use ($router) {

        $media = S3Helper::retrieve(EBUCKET_LOCATION::GALLERY, $uuid);

        if(!isset($media)){
            $router->trigger404();
        }

        header('Content-Type: '.$media['type']); //Add JSON Header to all API routes

        echo $media['body'];
    });

    $router->get('/game/{type}/{uuid}', function($type, $uuid) use ($router) {

        $bucketLocation = EBUCKET_LOCATION::NONE;

        switch ($type) {
            case 'cover':
                $bucketLocation = EBUCKET_LOCATION::GAME_COVER;
                break;
            case 'thumb':
                $bucketLocation = EBUCKET_LOCATION::GAME_THUMB;
                break;
            case 'icon':
                $bucketLocation = EBUCKET_LOCATION::GAME_ICON;
                break;
            case 'achievement':
                $bucketLocation = EBUCKET_LOCATION::GAME_ACHIEVEMENT;
                break;
            case 'badge':
                $bucketLocation = EBUCKET_LOCATION::GAME_BADGE;
                break;
        }

        $img = S3Helper::retrieve($bucketLocation, $uuid);

        if(!isset($img)){
            if($type == 'icon'){
                $img = S3Helper::retrieve(EBUCKET_LOCATION::GAME_ICON, "default");
            } elseif($type == 'achievement'){
                $img = S3Helper::retrieve(EBUCKET_LOCATION::GAME_ACHIEVEMENT, "default");
            } else {
            $router->trigger404();
            }
        }

        header('Content-Type: '.$img['type']); //Add JSON Header to all API routes

        echo $img['body'];
    });
});

$router->set404('/media(/.*)?', function() {
    header('HTTP/1.1 404 Not Found');
    
    $img = S3Helper::retrieve(EBUCKET_LOCATION::MISC, "404.png");

    $bodyData = $img['body']->getContents();

    header('Content-Type: image/jpeg');
    echo $bodyData;
});

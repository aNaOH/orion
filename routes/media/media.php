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
});

$router->set404('/media(/.*)?', function() {
    header('HTTP/1.1 404 Not Found');
    
    $img = S3Helper::retrieve(EBUCKET_LOCATION::MISC, "404.png");

    header('Content-Type: '.$img['type']); //Add JSON Header to all API routes

    echo $img['body'];
});
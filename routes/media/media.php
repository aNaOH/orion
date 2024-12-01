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

        if($uuid == "default"){
            $uuid .= ".png";
        }

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
});

$router->set404('/media(/.*)?', function() {
    header('HTTP/1.1 404 Not Found');

    //$manager = new ImageManager(
    //    new Intervention\Image\Drivers\Gd\Driver()
    //);

    $requestedRoute = $_SERVER['REQUEST_URI'];

    $img = S3Helper::retrieve(EBUCKET_LOCATION::MISC, "404.png");
    $bodyData = $img['body']->getContents();

    //$image = $manager->read($bodyData);

    // Modificar el regex para capturar el segmento después de /media/games/
    //if (preg_match('#^/media/games/([^/]+)#', $requestedRoute, $matches)) {
    //    $subroute = $matches[1]; 
    //    switch ($subroute) {
    //        case 'cover':
    //            $image->resize(600, 900);
    //            break;
    //    }
    //}

    header('Content-Type: image/jpeg');
    //echo $image->toJpeg();
    echo $bodyData;
});

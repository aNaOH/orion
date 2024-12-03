<?php

require_once 'controllers/HomeController.php';
require_once 'controllers/GameController.php';

$router->mount('/dev', function() use ($router) {

    $router->get('/', function(){
        HomeController::devDo();
    });

    $router->post('/game', function(){
        $title = $_POST['title'];
        $shortDescription = $_POST['shortDescription'];
        $asEditor = $_POST['asEditor'] == 'false' ? false : true;
        $developerName = $_POST['developerName'];

        GameController::newGame($title, $shortDescription, $asEditor, $developerName);
    });

    $router->post('/game/store', function(){
        $gameID = $_POST['game'];

        $game = Game::getById($gameID);
        if(is_null($game)){
            header('HTTP/1.1 400 Bad Request');
            $response['status'] = 400;
            $response['message'] = "No existe ningún juego con ese ID. ($gameID)";
    
            echo json_encode($response);
            exit();
        }

        $title = $_POST['title'];
        $shortDescription = $_POST['shortDescription'];
        $asEditor = $_POST['asEditor'] == 'false' ? false : true;
        $developerName = $_POST['developerName'];
        $description = $_POST['description'];
        $price = $_POST['price'];
        $discount = $_POST['discount'];

        $cover = $_FILES['coverFile'] ?? null;
        $thumb = $_FILES['thumbFile'] ?? null;

        $game->title = $title;
        $game->short_description = $shortDescription;
        $game->as_editor = $asEditor;
        $game->developer_name = $developerName;
        $game->description = $description;
        $game->base_price = $price;
        $game->discount = $discount / 100;

        $game->save();

        if(!is_null($cover)){
            S3Helper::upload(EBUCKET_LOCATION::GAME_COVER, $game->id, null, $cover['type'], $cover['tmp_name']);
        }

        if(!is_null($thumb)){
            S3Helper::upload(EBUCKET_LOCATION::GAME_THUMB, $game->id, null, $thumb['type'], $thumb['tmp_name']);
        }

        header('HTTP/1.1 200 OK');
        $response['status'] = 200;
        $response['message'] = "Juego editado ( ID: ".strval($game->id)." )";

        echo json_encode($response);
        exit();

    });

    $router->post('/game/build', function(){
        $gameID = $_POST['game'];

        $game = Game::getById($gameID);
        if(is_null($game)){
            header('HTTP/1.1 400 Bad Request');
            $response['status'] = 400;
            $response['message'] = "No existe ningún juego con ese ID. ($gameID)";
    
            echo json_encode($response);
            exit();
        }

        FormHelper::ValidateRequiredField($_POST['version'], 'version');
        FormHelper::ValidateRequiredFile($_FILES['file'], 'file');

        $version = $_POST['version'];
        $file = $_FILES['file'];

        $build = new Build($game->id, $version);
        
        if(!$build->setFile($file) || !$build->save()){
            header('HTTP/1.1 500 Internal Server Error');
            $response['status'] = 500;
            $response['message'] = "Fallo al subir la compilación, prueba más tarde";
            $response['field'] = "file";

            echo json_encode($response);
            exit();
        }


        header('HTTP/1.1 200 OK');
        $response['status'] = 200;
        $response['message'] = "Compilación subida ( Game ID: ".strval($game->id)." Version: ".$version." )";

        echo json_encode($response);
        exit();

    });

    $router->post('/game/public', function(){
        $gameID = $_POST['game'];

        $game = Game::getById($gameID);
        if(is_null($game)){
            header('HTTP/1.1 400 Bad Request');
            $response['status'] = 400;
            $response['message'] = "No existe ningún juego con ese ID. ($gameID)";
    
            echo json_encode($response);
            exit();
        }

        $isPublic = $_POST['isPublic'] == "true";
        $game->is_public = $isPublic;
        $game->save();

        header('HTTP/1.1 200 OK');
        $response['status'] = 200;
        $response['message'] = "Compilación subida ( Game ID: ".strval($game->id)." ¿Publico?: ".$isPublic." )";
        $response['newStatus'] = $isPublic ? 'public' : 'hidden';

        echo json_encode($response);
        exit();

    });

});
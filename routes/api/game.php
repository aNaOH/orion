<?php

require_once 'models/Game.php';
require_once 'models/Achievement.php';


$router->mount('/game', function() use ($router) {

    $router->post('/achievements', function(){
        $game = $_POST['game'];
        
        $achievements = Achievement::getAllByGame($game);

        if(count($achievements) == 0){
            header('HTTP/1.1 400 Bad Request');
            $response['status'] = 400;
            $response['message'] = "No hay logros para este juego.";
    
            echo json_encode($response);
            exit();
        }

        header('HTTP/1.1 200 OK');
        $response['achievements'] = [];
        foreach ($achievements as $a) {
            $response['achievements'][] = [
                'id' => $a->id,
                'name' => $a->name,
                'description' => $a->description,
                'unlockedIMG' => $a->icon,
                'lockedIMG' => $a->locked_icon,
                'isSecret' => $a->secret,
                'type' => $a->type->value,
                'statID' => $a->stat_id,
                'statValue' => $a->stat_value
            ];
        }
        echo json_encode($response);
        exit();
    });
});
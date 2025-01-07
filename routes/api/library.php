<?php

$router->mount('/library', function() use ($router) {

    $router->get('/', function(){
        $response = [];
        if(!isset($_SESSION['user'])){
            //Use unauthorized status code
            http_response_code(401);
            $response['status'] = 401;
            $response['message'] = 'User not logged in';
            $response['to'] = '/login';
            echo json_encode($response);
            return;
        }
    
        $user = User::getById($_SESSION['user']['id']);
    
        if(!isset($user)){
            http_response_code(401);
            $response['status'] = 401;
            $response['message'] = 'User not logged in';
            $response['to'] = '/logout?to=login';
            echo json_encode($response);
            return;
        }
    
        $games = $user->getAdquiredGames();

        foreach($games as $game){
            $response['data'][] = [
                'id' => $game->id,
                'title' => $game->title,
                'isDeveloper' => ($game->getDeveloper()->getOwner()->id == $user->id)
            ];
        }
    
        echo json_encode($response);
    });

    $router->get('/(\d+)', function($id){
        $response = [];
        if(!isset($_SESSION['user'])){
            //Use unauthorized status code
            http_response_code(401);
            $response['status'] = 401;
            $response['message'] = 'User not logged in';
            $response['to'] = '/login';
            echo json_encode($response);
            return;
        }
    
        $user = User::getById($_SESSION['user']['id']);
    
        if(!isset($user)){
            http_response_code(401);
            $response['status'] = 401;
            $response['message'] = 'User not logged in';
            $response['to'] = '/logout?to=login';
            echo json_encode($response);
            return;
        }

        if(!$user->hasAdquiredGame($id)){
            http_response_code(401);
            $response['status'] = 401;
            $response['message'] = 'User has not adquired this game';
            echo json_encode($response);
            return;
        }
    
        $game = Game::getById($id);

        $response['data'] = [
            'id' => $game->id,
            'title' => $game->title,
            'description' => $game->description,
            'isDeveloper' => ($game->getDeveloper()->getOwner()->id == $user->id)
        ];
    
        echo json_encode($response);
    });

});
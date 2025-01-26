<?php

require_once "models/User.php";

$router->mount('/lib', function() use ($router) {

    $router->post('/login', function(){
        $email = $_POST['email'];
        $password = $_POST['password'];
        $expirationString = $_POST['expirationString'];

        $user = User::getByEmail($email);

        $response = [];

        if(!isset($user)){
            header('HTTP/1.1 400 Bad Request');
            $response['status'] = 400;
            $response['message'] = "No existe un usuario con ese correo electrónico.";
            $response['value'] = $email;
            $response['field'] = "email";

            echo json_encode($response);
            exit();
        }

        if(!password_verify($password, $user->getPassword())){
            header('HTTP/1.1 400 Bad Request');
            $response['status'] = 400;
            $response['message'] = "Contraseña incorrecta";
            $response['field'] = "password";

            echo json_encode($response);
            exit();
        }

        $token = ClientToken::createToken($user->id, $expirationString);

        header('HTTP/1.1 200 OK');
        $response["token"] = $token;
        $response["displayName"] = $user->username;
        $response["id"] = $user->id;

        echo json_encode($response);
    });

    $router->post('/validate', function(){
        $token = $_POST['token'];
        $id = $_POST['id'];

        $response = [];

        $response['result'] = ClientToken::validateToken($token, $id);

        header('HTTP/1.1 200 OK');
        echo json_encode($response);
    });

    $router->get('/profile/{id}', function($id){
        $user = User::getById($id);

        if(is_null($user)){
            header('HTTP/1.1 400 Bad Request');
            $response['status'] = 400;
            $response['message'] = "No existe un usuario con ese ID.";
            $response['value'] = $id;
            $response['field'] = "id";

            echo json_encode($response);
            exit();
        }

        header('HTTP/1.1 200 OK');
        $response['id'] = $user->id;
        $response['username'] = $user->username;
        $response['motd'] = $user->motd;
        $response['profilePicUUID'] = $user->profile_pic ?? "default";

        echo json_encode($response);
    });

    $router->post('/ownership', function(){
        $token = $_POST['token'];
        $gameId = $_POST['game'];
        $userId = $_POST['user'];

        $tokenValid = ClientToken::validateToken($token, $userId);

        $response = [];

        if(!$tokenValid){
            header('HTTP/1.1 400 Bad Request');
            $response['result'] = false;

            echo json_encode($response);
            exit();
        }

        $user = User::getById($userId);

        $response['result'] = $user->hasAdquiredGame($gameId);

        header('HTTP/1.1 200 OK');
        echo json_encode($response);
    });

    $router->post('/achievement', function(){
        $token = $_POST['token'];
        $achievementId = $_POST['achievement'];
        $gameId = $_POST['game'];
        $userId = $_POST['user'];

        $tokenValid = ClientToken::validateToken($token, $userId);

        $response = [];

        if(!$tokenValid){
            header('HTTP/1.1 400 Bad Request');
            $response['result'] = false;

            echo json_encode($response);
            exit();
        }

        $user = User::getById($userId);

        if(!$user->hasAdquiredGame($gameId)){
            header('HTTP/1.1 400 Bad Request');
            $response['result'] = false;

            echo json_encode($response);
            exit();
        }

        $achievement = Achievement::getById($achievementId);
        if(is_null($achievement)){
            header('HTTP/1.1 400 Bad Request');
            $response['result'] = false;

            echo json_encode($response);
            exit();
        }

        if($achievement->game_id != $gameId){
            header('HTTP/1.1 400 Bad Request');
            $response['result'] = false;

            echo json_encode($response);
            exit();
        }

        if($user->hasUnlockedAchievement($achievement)){
            header('HTTP/1.1 200 OK');
            $response['result'] = false;

            echo json_encode($response);
            exit();
        }

        if($achievement->type !== EACHIEVEMENT_TYPE::TRIGGERED){
            header('HTTP/1.1 400 Bad Request');
            $response['result'] = false;

            echo json_encode($response);
            exit();
        }

        $response['result'] = $user->unlockAchievement($achievement);

        header('HTTP/1.1 200 OK');
        echo json_encode($response);
    });
});
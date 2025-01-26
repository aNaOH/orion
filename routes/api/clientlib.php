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

});
<?php

require './models/User.php';

class UserController {

    public static function register($email, $password, $confirmPassword){
        $response = array();
        
        $user = User::getByEmail($email);

        if(isset($user)){
            header('HTTP/1.1 400 Bad Request');
            $response['status'] = 400;
            $response['message'] = "An user with that email exists";
            $response['field'] = "emailAddress";

            echo json_encode($response);
            exit();
        }

        if($password != $confirmPassword){
            header('HTTP/1.1 400 Bad Request');
            $response['status'] = 400;
            $response['message'] = "Passwords must coincide";
            $response['field'] = "confirmPassword";

            echo json_encode($response);
            exit();
        }

        $username = explode("@", $email)[0];

        $user = new User($email, $username, $password, EUSER_TYPE::USER);
        $user->save();

        header('HTTP/1.1 200 OK');
        $response['status'] = 200;
        $response['message'] = "User created ( ID: ".strval($user->id)." )";

        echo json_encode($response);
        exit();
    }

    public static function login($email, $password){
        $response = array();
        
        $user = User::getByEmail($email);

        if(!isset($user)){
            header('HTTP/1.1 400 Bad Request');
            $response['status'] = 400;
            $response['message'] = "An user with that email does not exist";
            $response['field'] = "emailAddress";

            echo json_encode($response);
            exit();
        }

        if(!password_verify($password, $user->password)){
            header('HTTP/1.1 400 Bad Request');
            $response['status'] = 400;
            $response['message'] = "Incorrect password";
            $response['field'] = "password";

            echo json_encode($response);
            exit();
        }

        $_SESSION['user'] = $user->toSessionArray();

        header('HTTP/1.1 200 OK');
        $response['status'] = 200;
        $response['message'] = "User with ID ".strval($user->id)." logged in";

        echo json_encode($response);
        exit();
    }
}
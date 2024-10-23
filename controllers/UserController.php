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

            echo json_encode($response);
            exit();
        }

        if($password != $confirmPassword){
            header('HTTP/1.1 400 Bad Request');
            $response['status'] = 400;
            $response['message'] = "Passwords must coincide";

            echo json_encode($response);
            exit();
        }

        $username = explode("@", $email)[0];

        $user = new User($email, $username, $password, EUSER_TYPE::USER);
        $user->save();
    }
}
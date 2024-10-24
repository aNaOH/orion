<?php

require './models/User.php';

class UserController {

    public static function register($email, $password, $confirmPassword, $terms){
        $response = array();

        FormHelper::ValidateRequiredField($email, "emailAddress");
        FormHelper::ValidateRequiredField($password, "password");
        FormHelper::ValidateRequiredField($confirmPassword, "confirmPassword");
        
        FormHelper::ValidateMinChars($password, 8, "password");
        FormHelper::ValidatePasswordRequirements($password, "password");

        if(!isset($terms) || $terms != "yeah"){
            header('HTTP/1.1 400 Bad Request');
            $response['status'] = 400;
            $response['message'] = "Debes aceptar los términos y condiciones y la política de privacidad";
            $response['field'] = "terms";

            echo json_encode($response);
            exit();
        }
        
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
            $response['message'] = "Las contraseñas deben coincidir.";
            $response['field'] = "confirmPassword";

            echo json_encode($response);
            exit();
        }

        $username = explode("@", $email)[0];

        $user = new User($email, $username, $password, EUSER_TYPE::USER);
        $user->save();

        header('HTTP/1.1 200 OK');
        $response['status'] = 200;
        $response['message'] = "Usuario creado ( ID: ".strval($user->id)." )";

        echo json_encode($response);
        exit();
    }

    public static function login($email, $password){
        $response = array();

        FormHelper::ValidateRequiredField($email, "emailAddress");
        FormHelper::ValidateRequiredField($password, "password");

        FormHelper::ValidateMinChars($password, 8, "password");
        
        $user = User::getByEmail($email);

        if(!isset($user)){
            header('HTTP/1.1 400 Bad Request');
            $response['status'] = 400;
            $response['message'] = "No existe un usuario con ese correo electrónico.";
            $response['value'] = $email;
            $response['field'] = "emailAddress";

            echo json_encode($response);
            exit();
        }

        if(!password_verify($password, $user->password)){
            header('HTTP/1.1 400 Bad Request');
            $response['status'] = 400;
            $response['message'] = "Contraseña incorrecta";
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
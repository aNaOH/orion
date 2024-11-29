<?php

class FormHelper {

    public static function ValidateRequiredField($field, $fieldId){
        $response = [];

        if(!isset($field) || strlen($field) == 0){
            header('HTTP/1.1 400 Bad Request');
            $response['status'] = 400;
            $response['message'] = "Este campo es obligatorio.";
            $response['field'] = $fieldId;

            echo json_encode($response);
            exit();
        }
    }

    public static function ValidateEmailField($email, $fieldId){
        $response = [];
    
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            header('HTTP/1.1 400 Bad Request');
            $response['status'] = 400;
            $response['message'] = "Este campo debe contener un correo electrónico válido.";
            $response['field'] = $fieldId;
    
            echo json_encode($response);
            exit();
        }
    }
    

    public static function ValidateMinChars($field, $minChars, $fieldId){
        $response = [];

        if(strlen($field) < $minChars){
            header('HTTP/1.1 400 Bad Request');
            $response['status'] = 400;
            $response['message'] = "Introduce mínimo ".strval($minChars)." carácteres.";
            $response['field'] = $fieldId;

            echo json_encode($response);
            exit();
        }
    }

    public static function ValidatePasswordRequirements($field, $fieldId){
        $regex = '/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[\W_]).+$/';
        $response = [];

        if(!preg_match($regex, $field)){
            header('HTTP/1.1 400 Bad Request');
            $response['status'] = 400;
            $response['message'] = "La contraseña debe tener al menos una mayúscula, minúscula, un número y un carácter especial.";
            $response['field'] = $fieldId;

            echo json_encode($response);
            exit();
        }
    }

    public static function ValidateMinAge($field, $age, $fieldId){
        $response = [];

        $userBirthdate = new DateTime($field);

        $userAge = $userBirthdate->diff(new DateTime());

        if($userAge->y < $age){
            header('HTTP/1.1 400 Bad Request');
            $response['status'] = 400;
            $response['message'] = "No cumples con la edad requerida para crear una cuenta (".strval($age).", actualmente ".strval($userAge->y).").";
            $response['field'] = $fieldId;

            echo json_encode($response);
            exit();
        }
    }

    public static function ValidateToken($field, $fieldId, ETOKEN_TYPE $type){
        $validated = false;
        $response = [];


        switch ($type) {
            case ETOKEN_TYPE::USERACTION:
                $validated = UserActionToken::validateToken($field);
                break;
            
            case ETOKEN_TYPE::AUTHFORM:
                $validated = AuthFormToken::validateToken($field);
                break;

            default:
                $validated = Token::validateToken($field);
                break;
        }

        if(!$validated){
            header('HTTP/1.1 400 Bad Request');
            $response['status'] = 400;
            $response['message'] = "El token no es valido";
            $response['field'] = $fieldId;

            echo json_encode($response);
            exit();
        }
    }
}
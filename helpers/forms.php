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
}
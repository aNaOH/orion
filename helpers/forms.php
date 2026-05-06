<?php

class FormHelper {

    private static function failValidation(int $status, string $message, ?string $fieldId = null): void
    {
        header("HTTP/1.1 $status " . ($status === 400 ? "Bad Request" : "Conflict"));
        $response = [
            "status" => $status,
            "message" => $message,
        ];

        if ($fieldId !== null) {
            $response["field"] = $fieldId;
        }

        echo json_encode($response);
        exit();
    }

    public static function ValidateRequiredField($field, $fieldId){
        if(!isset($field) || strlen($field) == 0){
            self::failValidation(400, "Este campo es obligatorio.", $fieldId);
        }
    }

    public static function ValidateRequiredFile($file, $fieldId){
        if(!isset($file) || !is_array($file)){
            self::failValidation(400, "Este campo es obligatorio.", $fieldId);
        }
    }

    public static function ValidateEmailField($email, $fieldId){
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            self::failValidation(400, "Este campo debe contener un correo electrónico válido.", $fieldId);
        }
    }
    

    public static function ValidateMinChars($field, $minChars, $fieldId){
        if(strlen($field) < $minChars){
            self::failValidation(400, "Introduce mínimo ".strval($minChars)." carácteres.", $fieldId);
        }
    }

    public static function ValidatePasswordRequirements($field, $fieldId){
        $regex = '/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[\W_]).+$/';

        if(!preg_match($regex, $field)){
            self::failValidation(400, "La contraseña debe tener al menos una mayúscula, minúscula, un número y un carácter especial.", $fieldId);
        }
    }

    public static function ValidateMinAge($field, $age, $fieldId){
        $userBirthdate = new DateTime($field);

        $userAge = $userBirthdate->diff(new DateTime());

        if($userAge->y < $age){
            self::failValidation(400, "No cumples con la edad requerida para crear una cuenta (".strval($age).", actualmente ".strval($userAge->y).").", $fieldId);
        }
    }

    public static function ValidateAllowedValue($field, array $allowedValues, $fieldId, ?string $message = null){
        if(!in_array($field, $allowedValues, true)){
            self::failValidation(400, $message ?? "El valor seleccionado no es válido.", $fieldId);
        }
    }

    public static function ValidateMaxChars($field, $maxChars, $fieldId){
        if(strlen($field) > $maxChars){
            self::failValidation(400, "Introduce máximo ".strval($maxChars)." carácteres.", $fieldId);
        }
    }

    public static function ValidateDateTimeField($field, $fieldId, ?string $message = null): DateTime
    {
        // Try both formats (with and without seconds) as browsers can send either
        $value = DateTime::createFromFormat('Y-m-d\TH:i:s', $field);
        if (!$value) {
            $value = DateTime::createFromFormat('Y-m-d\TH:i', $field);
        }

        $errors = DateTime::getLastErrors();

        if (
            !$value ||
            ($errors !== false && (($errors["warning_count"] ?? 0) > 0 || ($errors["error_count"] ?? 0) > 0))
        ) {
            self::failValidation(400, $message ?? "La fecha indicada no es válida.", $fieldId);
        }

        return $value;
    }

    public static function ValidateFutureDateTime(DateTime $field, $fieldId, ?string $message = null): void
    {
        $now = new DateTime();
        if ($field <= $now) {
            self::failValidation(400, $message ?? "La fecha indicada debe ser futura.", $fieldId);
        }
    }

    public static function ValidateNotSameValue($field, $forbiddenValue, $fieldId, ?string $message = null): void
    {
        if ((string) $field === (string) $forbiddenValue) {
            self::failValidation(400, $message ?? "Este valor no está permitido.", $fieldId);
        }
    }

    public static function ValidateBusinessRule(bool $condition, string $message, ?string $fieldId = null, int $status = 400): void
    {
        if (!$condition) {
            self::failValidation($status, $message, $fieldId);
        }
    }

    public static function FailWithMessage(string $message, ?string $fieldId = null, int $status = 400): void
    {
        self::failValidation($status, $message, $fieldId);
    }

    public static function ValidateToken($field, $fieldId, ETOKEN_TYPE $type){
        $validated = false;


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
            self::failValidation(400, "El token no es valido", $fieldId);
        }
    }
}

<?php

class Token {
    private static $magicWord = 'orion';

    public static function getDate() {
        return (new DateTime())->format('Y-m-d');
    }

    public static function createToken(){
        return Tript::encryptString(self::$magicWord.'_'.self::getDate());
    }

    public static function validateToken($token, &$tokenParts = []){
        $parsedToken = Tript::decryptString($token);

        $tokenParts = explode('_', $parsedToken);

        return self::validationRules($tokenParts);
    }

    private static function validationRules($tokenParts){
        return ($tokenParts[0] == self::$magicWord && $tokenParts[1] == getDate());
    }
}

class AuthFormToken {
    private static $magicWord = 'orionauth';

    public static function createToken(){
        return Tript::encryptString(self::$magicWord.'_'.self::getDate());
    }

    public static function getDate() {
        $endDate = new DateTime();

        $endDate->add(new DateInterval('PT30M'));

        return $endDate->format('Y-m-d H:i:s');
    }

    public static function validateToken($token, &$tokenParts = []){
        $parsedToken = Tript::decryptString($token);

        $tokenParts = explode('_', $parsedToken);

        return self::validationRules($tokenParts);
    }

    private static function validationRules($tokenParts){

        $dateExpiration = new DateTime($tokenParts[1]);

        $currentDate = new DateTime();

        return ($tokenParts[0] == self::$magicWord && $currentDate < $dateExpiration);
    }

}

class UserActionToken {
    private static $magicWord = 'orionuserdoes';

    public static function createToken(){
        return Tript::encryptString(self::$magicWord.'_'.self::getDate());
    }

    public static function getDate() {

        if(!isset($_SESSION['user'])) return '';

        $user = User::getById($_SESSION['user']['id']);

        if(is_null($user)) return '';

        //An user is provided, proceed.

        $endDate = new DateTime();

        $endDate->add(new DateInterval('PT30M'));

        return $endDate->format('Y-m-d H:i:s').'_'.strval($user->id);
    }

    public static function validateToken($token, &$tokenParts = []){
        $parsedToken = Tript::decryptString($token);

        $tokenParts = explode('_', $parsedToken);

        return self::validationRules($tokenParts);
    }

    private static function validationRules($tokenParts){

        if(!isset($_SESSION['user'])) return false;

        $user = User::getById($_SESSION['user']['id']);

        if(is_null($user)) return false;

        //An user is provided, proceed.

        $dateExpiration = new DateTime($tokenParts[1]);

        $currentDate = new DateTime();

        return (
                $tokenParts[0] == self::$magicWord && 
                $currentDate < $dateExpiration && 
                $tokenParts[2] == $user->id
            );
    }
}
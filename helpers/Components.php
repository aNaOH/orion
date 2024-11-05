<?php

require_once 'models/Game.php';

class OrionComponents {

    public static function GameCommunity(Game $game, string $class = "col-lg-3 col-md-4 col-sm-5 mb-4"){
        include 'components/GameCommunity.php';
    }

    public static function TokenInput(ETOKEN_TYPE $type){

        $token = '';

        switch ($type) {
            case ETOKEN_TYPE::COMMON:
                $token = Token::createToken();
                break;
            
            case ETOKEN_TYPE::AUTHFORM:
                $token = AuthFormToken::createToken();
                break;

            case ETOKEN_TYPE::USERACTION:
                $token = UserActionToken::createToken();
                break;
        }

        include 'components/TokenInput.php';
    }
    
}
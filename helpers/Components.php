<?php

require_once 'models/Game.php';

class OrionComponents {

    public static function GameCommunity(Game $game, string $class = "col-lg-3 col-md-4 col-sm-5 mb-4"){
        include 'components/GameCommunity.php';
    }

    public static function Comment(Comment $comment){
        include 'components/Comment.php';
    }

    public static function GalleryEntry(Post $post){
        if($post->type != EPOST_TYPE::GALLERY) return;
        $galleryInfo = $post->getPostInfo();

        $value = 0;

        if(isset($_SESSION['user'])){
            $value = $galleryInfo->getUserValue($_SESSION['user']['id']);
        }

        include 'components/GalleryEntry.php';
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
<?php

require_once 'models/Game.php';
require_once 'models/Post.php';

class PostController {

    //Esta función existe con el proposito de tener una alternativa para añadir juegos mientras la tienda y los desarrolladores no estén implementados
    public static function addPost(int $gameId, EPOST_TYPE $type, array $data){
        if(!isset($_SESSION['user'])) return false;
        
        $author = User::getById($_SESSION['user']['id']);

        if(is_null($author)) return false;

        $game = Game::getById($gameId);

        if(is_null($game)) return false;

        $post = new Post($data["title"], $data["body"], false, $type, $game->id, $author->id);

        if(!$post->save()) return false;

        switch ($type) {
            case EPOST_TYPE::GALLERY:
                $stuff = new GalleryEntry($post->id, "");
                $stuff->save();
                break;

            case EPOST_TYPE::GUIDE:
                $stuff = new Guide($post->id, $data['guide_type']);
                $stuff->save();
                break;
        }

        return true;
    }

    public static function getPosts(int $gameId, EPOST_TYPE $type){
        $game = Game::getById($gameId);

        if(is_null($game)) return false;

        $GLOBALS['game'] = $game;
        $GLOBALS['posts'] = Post::getAllByType($type);

        $typeString = '';

        switch ($type) {
            case EPOST_TYPE::POST:
                $typeString = "posts";
                break;
            
            case EPOST_TYPE::GALLERY:
                $typeString = "gallery";
                break;

            case EPOST_TYPE::GUIDE:
                $typeString = "guides";
                break;
        }

        include('views/community/'.$typeString.'/index.php');
    }

    public static function createPost(int $gameId, EPOST_TYPE $type){
        $game = Game::getById($gameId);

        if(is_null($game)) return false;

        $GLOBALS['game'] = $game;

        $typeString = '';

        switch ($type) {
            case EPOST_TYPE::POST:
                $typeString = "posts";
                break;
            
            case EPOST_TYPE::GALLERY:
                $typeString = "gallery";
                break;

            case EPOST_TYPE::GUIDE:
                $typeString = "guides";
                break;
        }

        include('views/community/'.$typeString.'/create.php');
    }

}
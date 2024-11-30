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
        $GLOBALS['posts'] = Post::getAllByTypeAndGame($type, $gameId);

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

    public static function getPost(int $gameId, EPOST_TYPE $type, int $postId){
        $game = Game::getById($gameId);

        if(is_null($game)) return false;

        $post = Post::getById($postId);

        if(is_null($post) || $post->game_id !== $game->id) return false;

        $GLOBALS['game'] = $game;
        $GLOBALS['post'] = $post;

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

        include('views/community/'.$typeString.'/post.php');
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
                $gTypes = GuideType::getAll();
                $GLOBALS['guideTypes'] = $gTypes;
                break;
        }

        include('views/community/'.$typeString.'/create.php');
    }

    public static function create(int $gameId, EPOST_TYPE $type, string $title, string $body, int $guideType = null){
        $game = Game::getById($gameId);

        if(is_null($game)) return false;

        if($type != EPOST_TYPE::GALLERY){
            $post = new Post($title, $body, true, $type, $game->id, $_SESSION['user']['id']);

            $post->save();

            if ($type == EPOST_TYPE::GUIDE) {

                if(!isset($guideType)) {
    
                    $post->delete();
    
                    header('HTTP/1.1 400 Bad Request');
                    $response['status'] = 400;
                    $response['message'] = "Guide type is not set";
    
                    echo json_encode($response);
                    exit();
                }
                $guide = new Guide($post->id, $guideType);
                $guide->save();
            }
        } else {
            if(!isset($_FILES['body'])) {

                header('HTTP/1.1 400 Bad Request');
                $response['status'] = 400;
                $response['message'] = "File not uploaded";

                echo json_encode($response);
                exit();
            }

            $media = $_FILES['body'];

            $filename = $body.'a'.strval($_SESSION['user']['id']).'g'.strval($gameId).'n'.strval(count(Post::getAllByTypeAndGame(EPOST_TYPE::GALLERY, $gameId)) + 1);
            $uuid = Tript::encryptString($filename);

            $post = new Post($title, "", true, $type, $game->id, $_SESSION['user']['id']);
            $post->save();

            $gallery = new GalleryEntry($post->id, $uuid);
            $gallery->save();

            S3Helper::upload(EBUCKET_LOCATION::GALLERY, $uuid, null, $media['type'], $media['tmp_name']);
        }


        header('HTTP/1.1 200 OK');
        $response['status'] = 200;
        $response['message'] = "Post creado ( ID: ".strval($post->id)." )";

        echo json_encode($response);
        exit();
    }

    public static function addComment($postId, string $body) {

        $author = null;

        if(isset($_SESSION['user'])){
            $author = User::getById($_SESSION['user']['id'] ?? -1);
        }
        
        if(is_null($author)) header('location: /login');
        
        $post = Post::getById($postId);

        if(is_null($post)) header('location: /communities');

        return $post->addComment($author->id, $body);
    }

    public static function vote($postId, int $value) {

        $voter = null;

        $jsonArray = array();

        $jsonArray['value'] = $value;
        $jsonArray['oldValue'] = $_POST['previousValue'] ?? 0;

        $voter = null;

        if(isset($_SESSION['user'])){
            $voter = User::getById($_SESSION['user']['id'] ?? -1);
        }
        
        if(is_null($voter)) {
            header('HTTP/1.1 401 Unauthorized');
            
            $jsonArray['status'] = "401";
            $jsonArray['status_text'] = "User not logged";
            
            echo json_encode($jsonArray);
            exit();
        }
        
        $post = Post::getById($postId);

        if(is_null($post)) {
            header('HTTP/1.1 400 Bad request');
            
            $jsonArray['status'] = "400";
            $jsonArray['status_text'] = "Post does not exist";
            
            echo json_encode($jsonArray);
            exit();
        }

        if($post->type != EPOST_TYPE::GALLERY) {
            header('HTTP/1.1 400 Bad request');
            
            $jsonArray['status'] = "400";
            $jsonArray['status_text'] = "Post is not a gallery entry";
            
            echo json_encode($jsonArray);
            exit();
        }

        $galleryInfo = $post->getPostInfo();

        if(is_null($galleryInfo)) {
            header('HTTP/1.1 400 Bad request');
            
            $jsonArray['status'] = "400";
            $jsonArray['status_text'] = "Post does not have an associated gallery entry info";
            
            echo json_encode($jsonArray);
            exit();
        }

        $galleryInfo->addVote($voter->id, $value);

        header('HTTP/1.1 200 OK');
            
        $jsonArray['status'] = "200";
        $jsonArray['status_text'] = "Post voted";
        $jsonArray['new_value'] = $galleryInfo->getValue();
            
        echo json_encode($jsonArray);
        exit();
    }
}
<?php

require_once "helpers/s3.php";

class MediaController
{
    public static function showProfilePic($uuid)
    {
        global $router;
        if ($uuid == "default")
            $uuid .= ".png";
        $img = S3Helper::retrieve(EBUCKET_LOCATION::PROFILE_PIC, $uuid);
        if (!isset($img)) {
            $router->trigger404();
            exit();
        }
        header("Content-Type: " . $img["type"]);
        S3Helper::streamToClient($img);
        exit();
    }

    public static function showGuideTypeIcon($uuid)
    {
        global $router;
        $img = S3Helper::retrieve(EBUCKET_LOCATION::GUIDE_TYPE_ICON, $uuid);
        if (!isset($img)) {
            $router->trigger404();
            exit();
        }
        header("Content-Type: " . $img["type"]);
        S3Helper::streamToClient($img);
        exit();
    }

    public static function showGalleryMedia($uuid)
    {
        global $router;
        $media = S3Helper::retrieve(EBUCKET_LOCATION::GALLERY, $uuid);
        if (!isset($media)) {
            $router->trigger404();
            exit();
        }
        header("Content-Type: " . $media["type"]);
        S3Helper::streamToClient($media);
        exit();
    }

    public static function showGameMedia($type, $uuid)
    {
        global $router;
        $loc = EBUCKET_LOCATION::NONE;
        switch ($type) {
            case "cover":
                $loc = EBUCKET_LOCATION::GAME_COVER;
                break;
            case "thumb":
                $loc = EBUCKET_LOCATION::GAME_THUMB;
                break;
            case "icon":
                $loc = EBUCKET_LOCATION::GAME_ICON;
                break;
            case "achievement":
                $loc = EBUCKET_LOCATION::GAME_ACHIEVEMENT;
                break;
            case "badge":
                $loc = EBUCKET_LOCATION::GAME_BADGE;
                break;
            case "feature":
                $loc = EBUCKET_LOCATION::GAME_FEATURE_ICON;
                break;
        }

        $img = S3Helper::retrieve($loc, $uuid);
        if (!isset($img)) {
            if ($type == "icon")
                $img = S3Helper::retrieve(EBUCKET_LOCATION::GAME_ICON, "default");
            elseif ($type == "achievement")
                $img = S3Helper::retrieve(EBUCKET_LOCATION::GAME_ACHIEVEMENT, "default");
            else {
                $router->trigger404();
                exit();
            }
        }
        header("Content-Type: " . $img["type"]);
        S3Helper::streamToClient($img);
        exit();
    }

    public static function handle404()
    {
        header("HTTP/1.1 404 Not Found");
        $img = S3Helper::retrieve(EBUCKET_LOCATION::MISC, "404.png");
        header("Content-Type: image/jpeg");
        S3Helper::streamToClient($img);
        exit();
    }
}


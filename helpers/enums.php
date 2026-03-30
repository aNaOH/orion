<?php

enum ETOKEN_TYPE: int
{
    case COMMON = 0;
    case AUTHFORM = 1;
    case USERACTION = 2;
    case USERLIBRARY = 3;
    case DEVACTION = 4;
}

enum EPOST_TYPE: int
{
    case POST = 0;
    case GALLERY = 1;
    case GUIDE = 2;
    case GAME_NEWS = 3;
}

enum EUSER_TYPE: int
{
    case USER = 0;
    case ADMIN = 1;
}

enum ESTAT_TYPE: int
{
    case INCREMENTAL = 0;
    case BEST = 1;
}

enum EBUCKET_LOCATION: string
{
    case MISC = "misc/";
    case PROFILE_PIC = "user/profile_pic/";
    case GALLERY = "community/gallery/";
    case GUIDE_TYPE_ICON = "community/guide/type/";
    case GAME_FEATURE_ICON = "game/feature/";
    case GAME_COVER = "game/cover/";
    case GAME_THUMB = "game/thumb/";
    case GAME_ICON = "game/icon/";
    case GAME_BADGE = "game/badge/";
    case GAME_ACHIEVEMENT = "game/achievement/";
    case GAME_BUILD = "game/build/";
    case NONE = "";
}

enum EACHIEVEMENT_TYPE: int
{
    case TRIGGERED = 0;
    case STAT = 1;
}

enum EUSER_RELATIONSHIP: int
{
    case NONE = -1;
    case PENDING = 0;
    case FRIEND = 1;
    case BLOCKED = 2;
}

?>

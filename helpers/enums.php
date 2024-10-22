<?php

enum EPOST_TYPE : int {
    case POST = 0;
    case GALLERY = 1;
    case GUIDE = 2;
}

enum EUSER_TYPE : int {
    case USER = 0;
    case ADMIN = 1;
}

enum ELEADERBOARD_TYPE : int {
    case INCREMENTAL = 0;
    case BEST = 1;
}

enum EBUCKET_LOCATION : string {
    case PROFILE_PIC = 'user/profile_pic/';
    case GALLERY = 'community/gallery/';
    case GUIDE_TYPE_ICON = 'community/guide/type/';
    case GAME_COVER = 'game/cover/';
    case GAME_THUMB = 'game/thumb/';
    case GAME_BADGE = 'game/badge/';
    case GAME_ACHIEVEMENT = 'game/achievement/';
}

?>
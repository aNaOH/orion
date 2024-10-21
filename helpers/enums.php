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

?>
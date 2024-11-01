<?php

require_once 'models/Game.php';

class OrionComponents {

    public static function GameCommunity(Game $game, string $class = "col-lg-3 col-md-4 col-sm-5 mb-4"){
        include 'components/GameCommunity.php';
    }
    
}
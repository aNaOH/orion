<?php

$router->get("/games/{gameId}/community/news", 'DevController::showPanelGameNews');
$router->get("/games/{gameId}/community/news/new", 'DevController::showPanelGameNewNews');
$router->get("/games/{gameId}/community/news/{newsId}/edit", 'DevController::showPanelGameEditNews');


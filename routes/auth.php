<?php

require_once "controllers/UserController.php";

$router->get("/register", 'UserController::showRegister');
$router->get("/login", 'UserController::showLogin');
$router->get("/logout", 'UserController::logout');
$router->get("/profile", 'UserController::showProfile');

$router->get("/profile/friends", 'UserController::showFriendsList');
$router->get("/suspended", 'UserController::showSuspended');

$router->get("/library", 'UserController::showLibrary');

$router->get("/library/{gameid}/{version}", 'UserController::downloadGame');

$router->get("/profile/edit", 'UserController::showProfileEdit');

$router->get("/profile/(\d+)", 'UserController::showPublicProfile');


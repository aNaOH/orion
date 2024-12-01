<?php

$router->before('GET|POST', '/admin(/.*)?', function() {

    if (isset($_SESSION['user'])) {

        $user = User::getById($_SESSION['user']['id']);
        if($user){
            if($user->role != EUSER_TYPE::ADMIN){
                header('location: /');
                exit();
            }
        }
        else{
            header('location: /');
            exit();
        }
    }
    else {
        header('location: /');
        exit();
    }
});

$router->before('GET|POST', '/dev/panel(/.*)?', function() {

    if (isset($_SESSION['user'])) {

        $user = User::getById($_SESSION['user']['id']);
        if($user){
            if(is_null($user->getDeveloperInfo())){
                header('location: /');
                exit();
            }
        }
        else{
            header('location: /');
            exit();
        }
    }
    else {
        header('location: /');
        exit();
    }
});

$router->before('GET|POST', '/stripe(/.*)?', function() {

    if (isset($_SESSION['user'])) {

        $user = User::getById($_SESSION['user']['id']);
        if(is_null($user)){
            header('location: /');
            exit();
        }
    }
    else {
        header('location: /');
        exit();
    }
});

$router->before('GET|POST', '/api(/.*)?', function() {
    header('Content-Type: application/json'); //Add JSON Header to all API routes
});
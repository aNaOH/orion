<?php

$router->before('GET|POST', '/admin/.*', function() {
    if (isset($_SESSION['user'])) { //Complete this
        $user = User::getById($_SESSION['user']['id']);
        if($user){
            if($user->role != EUSER_TYPE::ADMIN){
                header('location: /auth/login');
                exit();
            }
        }
        else{
            header('location: /auth/login');
            exit();
        }
    }
    else {
        header('location: /auth/login');
        exit();
    }
});

$router->before('GET|POST', '/api/.*', function() {
    header('Content-Type: application/json'); //Add JSON Header to all API routes
});
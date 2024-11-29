<?php

$router->get('/register', function(){
    if(isset($_SESSION['user'])){
        header('location: /');
    }
    include('views/auth/register.php');
});

$router->get('/login', function(){
    if(isset($_SESSION['user'])){
        header('location: /');
    }
    include('views/auth/login.php');
});

$router->get('/logout', function(){
    if(isset($_SESSION['user'])){
        session_destroy();
    }
    header('location: /');
});

$router->get('/profile', function(){
    if(!isset($_SESSION['user'])){
        header('location: /');
    }

    $user = User::getById($_SESSION['user']['id']);

    if(!isset($user)){
        header('location: /logout');
    }

    $GLOBALS['user'] = $user;
    $GLOBALS['is_self'] = true;

    include('views/auth/profile.php');
});

$router->get('/profile/edit', function(){
    if(!isset($_SESSION['user'])){
        header('location: /');
    }

    $user = User::getById($_SESSION['user']['id']);

    if(!isset($user)){
        header('location: /logout');
    }

    $GLOBALS['user'] = $user;

    include('views/auth/profileEdit.php');
});

$router->get('/profile/(\d+)', function($userId) use ($router) {
    
    if(isset($_SESSION['user'])){
        if($_SESSION['user']['id'] == $userId) {
            header('location: /profile');
        }
    }

    $user = User::getById($userId);
    
    if(!isset($user)){
        $router->trigger404();
        exit();
    }

    $GLOBALS['user'] = $user;

    include('views/auth/profile.php');
});

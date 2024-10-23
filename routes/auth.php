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
    include('views/auth/profile.php');
});


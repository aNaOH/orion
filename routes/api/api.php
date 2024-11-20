<?php

//API
$router->mount('/api', function() use ($router) {

    $router->post('/auth/login', function(){
        $email = $_POST['email'];
        $password = $_POST['password'];

        UserController::login($email, $password);
    });

    $router->post('/auth/register', function(){
        $email = $_POST['email'];
        $password = $_POST['password'];
        $confirmPassword = $_POST['confirmPassword'];
        $terms = $_POST['terms'];
        $birthdate = $_POST['birthdate'];

        UserController::register($email, $password, $confirmPassword, $terms);
    });

    include('routes/api/community.php');

    include('routes/api/admin.php');
});


$router->set404('/api(/.*)?', function() {
    header('HTTP/1.1 404 Not Found');
    header('Content-Type: application/json');

    $jsonArray = array();
    $jsonArray['status'] = "404";
    $jsonArray['status_text'] = "route not defined";

    echo json_encode($jsonArray);
});
<?php

require 'vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable('./');
$dotenv->load();

require 'models/conn.php'; //Import DB model
require 'controllers/UserController.php'; //Import DB model

define("ORION_DB", Connection::connectToDB($_ENV['DB_HOST'], $_ENV['DB_NAME'], $_ENV['DB_USER'], $_ENV['DB_PASS'])); //Connect to DB and entering into constant

session_start([
    "name" => "ORION_SESSION"
]);

// Create Router instance
$router = new \Bramus\Router\Router();

$router->get('/', function(){
    if(isset($_SESSION['user'])){
        $userSession = $_SESSION['user'];
    }
    include('views/index.php');
});

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


//Middlewares

$router->before('GET|POST', '/admin/.*', function() {
    if (!isset($_SESSION['user'])) { //Complete this
        header('location: /auth/login');
        exit();
    }
});

$router->before('GET|POST', '/api/.*', function() {
    header('Content-Type: application/json'); //Add JSON Header to all API routes
});

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

        UserController::register($email, $password, $confirmPassword);
    });
});

$router->set404(function() {
    header('HTTP/1.1 404 Not Found');
    include('views/404.php');
});

$router->set404('/api(/.*)?', function() {
    header('HTTP/1.1 404 Not Found');
    header('Content-Type: application/json');

    $jsonArray = array();
    $jsonArray['status'] = "404";
    $jsonArray['status_text'] = "route not defined";

    echo json_encode($jsonArray);
});

// Run it!
$router->run();

?>
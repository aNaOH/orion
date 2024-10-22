<?php

require 'vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable('./');
$dotenv->load();

require 'models/conn.php'; //Import DB model
require 'models/User.php'; //Import DB model

define("ORION_DB", Connection::connectToDB($_ENV['DB_HOST'], $_ENV['DB_NAME'], $_ENV['DB_USER'], $_ENV['DB_PASS'])); //Connect to DB and entering into constant

// Create Router instance
$router = new \Bramus\Router\Router();

$router->get('/', function(){
    $user = User::getById(0);
    include('views/index.php');
});


// Run it!
$router->run();

?>
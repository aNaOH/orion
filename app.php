<?php

require 'vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable('./');
$dotenv->load();

require_once 'models/conn.php'; //Import DB model

define("ORION_DB", Connection::connectToDB($_ENV['DB_HOST'], $_ENV['DB_NAME'], $_ENV['DB_USER'], $_ENV['DB_PASS'])); //Connect to DB and entering into constant

session_start([
    "name" => "ORION_SESSION"
]);

// Create Router instance
$router = new \Bramus\Router\Router();

$router->get('/', function(){
    include('views/index.php');
});

include('routes/auth.php');

include('routes/game/community.php');

include('routes/admin/admin.php');

include('routes/media/media.php');

include('routes/legal.php');

include('routes/api/api.php');

include('routes/middlewares.php');

$router->set404(function() {
    header('HTTP/1.1 404 Not Found');
    include('views/404.php');
});

// Run it!
$router->run();

?>
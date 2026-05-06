<?php

date_default_timezone_set('UTC');

require "vendor/autoload.php";

$dotenv = Dotenv\Dotenv::createImmutable("./");
$dotenv->load();

define(
    "ORION_DB",
    Connection::connectToDB(
        $_ENV["DB_HOST"],
        $_ENV["DB_NAME"],
        $_ENV["DB_USER"],
        $_ENV["DB_PASS"],
    ),
); //Connect to DB and entering into constant

session_start([
    "name" => "ORION_SESSION",
]);

// Create Router instance
$router = new \Bramus\Router\Router();

$router->get("/", [StorefrontController::class, "showHome"]);

include "routes/routes.php";

include "routes/middlewares.php";

$router->set404(function () {
    header("HTTP/1.1 404 Not Found");
    ViewController::render("errors/404");
});

// Run it!
$router->run();

?>
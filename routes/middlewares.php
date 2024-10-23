<?php

$router->before('GET|POST', '/admin/.*', function() {
    if (!isset($_SESSION['user'])) { //Complete this
        header('location: /auth/login');
        exit();
    }
});

$router->before('GET|POST', '/api/.*', function() {
    header('Content-Type: application/json'); //Add JSON Header to all API routes
});
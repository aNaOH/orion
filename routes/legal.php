<?php

$router->get('/legal', function(){
    include('views/index.php');
});

$router->get('/legal/terms', function(){
    include('views/legal/terms.php');
});

$router->get('/legal/privacy', function(){
    include('views/legal/privacy.php');
});

$router->get('/legal/cookies', function(){
    include('views/legal/cookies.php');
});

$router->get('/legal/refund', function(){
    include('views/legal/refund.php');
});
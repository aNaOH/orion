<?php

$router->get('/legal', function(){
    include('views/index.php');
});

$router->get('/legal/terms', function(){
    ViewController::render('legal/terms', ['title' => 'Términos y condiciones de Orion']);
});

$router->get('/legal/privacy', function(){
    ViewController::render('legal/privacy', ['title' => 'Política de privacidad de Orion']);
});

$router->get('/legal/cookies', function(){
    ViewController::render('legal/cookies', ['title' => 'Política de cookies de Orion']);
});

$router->get('/legal/refund', function(){
    ViewController::render('legal/refund', ['title' => 'Política de reembolso y devoluciones de Orion']);
});
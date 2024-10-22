<?php

require_once __DIR__.'/router.php';

get('/', 'views/index.php');

get('/user/$id', 'views/user');

get('/callback', function(){
  echo 'Callback executed';
});

get('/callback/$name/$last_name', function($name, $last_name){
  echo "Callback executed. The full name is $name $last_name";
});

// ##################################################
// AUTH ROUTES

get('/register', 'views/auth/register.php');


// ##################################################


// ################## API ROUTES ####################

post('/api/auth', function() {
    echo 'lol';
});

// 404 page
any('/404','views/404.php');

<?php

require('./vendor/autoload.php'); //Import Composer installed libraries

$dotenv = Dotenv\Dotenv::createImmutable('./');
$dotenv->load();

include('./routes.php'); //Execute Routing
?>
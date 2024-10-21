<?php

require('../models/conn.php'); //Import DB model

$dbConn = Connection::connectToDB($_ENV['DB_HOST'], $_ENV['DB_NAME'], $_ENV['DB_USER'], $_ENV['DB_PASS']); //Connect to DB

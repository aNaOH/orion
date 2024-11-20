<?php

if(isset($_SESSION['user'])){
    $userSession = $_SESSION['user'];
}

include('header.php');

showPage();

include('footer.php');
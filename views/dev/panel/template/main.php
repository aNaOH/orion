<?php

if(isset($_SESSION['user'])){
    $userSession = $_SESSION['user'];
}

include('views/dev/panel/template/header.php');

showPage();

include('views/dev/panel/template/footer.php');
<?php

function APIRegister($post){
    $username = $post['username'];
    $password = $post['password'];
    $confirmPassword = $post['confirmPassword'];

    echo $username;
    echo $password;
    echo $confirmPassword;
}
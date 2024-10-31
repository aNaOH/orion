<?php

class NavbarHelper {
    public static function getUserNavbar($userSession){
        if(isset($userSession)){
            ?>

            <li class="dropdown"><a href="#"><span><?= $userSession['username'] ?></span> <i class="bi bi-chevron-down toggle-dropdown"></i></a>
                <ul>
                    <li><a href="/profile">Perfil</a></li>
                    <?php
                        $user = User::getById($userSession['id']);

                        if($user->role == EUSER_TYPE::ADMIN)
                        {
                    ?>
                    <li><a href="/admin">Panel de administración</a></li>
                    <?php } ?>
                    <?php
                        $user = User::getById($userSession['id']);

                        if(!is_null($user->getDeveloperInfo()))
                        {
                    ?>
                    <li><a href="/dev/panel">Panel de desarrollador</a></li>
                    <?php } ?>
                    <li><a href="/logout">Cerrar sesión</a></li>
                </ul>
            </li>

            <?php
        } else {
            ?>

            <li class="dropdown"><a href="#"><span>Cuenta</span> <i class="bi bi-chevron-down toggle-dropdown"></i></a>
                <ul>
                    <li><a href="/login">Iniciar sesión</a></li>
                    <li><a href="/register">Crear una cuenta</a></li>
                </ul>
            </li>

            <?php
        }
    }
}
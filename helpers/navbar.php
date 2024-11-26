<?php

class NavbarHelper {
    public static function getUserNavbar($userSession){
        $user = key_exists("id", $userSession) ? User::getById($userSession['id']) : null;
        if(isset($user)){
            ?>

                <button id="accountDropdown" class="account-dropdown flex items-center hover:text-gray-300 focus:outline-none" aria-haspopup="true" aria-expanded="false">
                    <?= $userSession['username'] ?>
                    <svg xmlns="http://www.w3.org/2000/svg" class="dropdown-icon h-4 w-4 ml-1 transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
                <ul id="accountMenu" class="hidden absolute right-0 mt-2 py-2 w-48 bg-gray-800 rounded-md shadow-xl z-20" role="menu" aria-orientation="vertical" aria-labelledby="accountDropdown">
                    <li>
                        <a href="/profile" class="block px-4 py-2 text-sm text-gray-300 hover:bg-gray-700 hover:text-white transition-colors duration-200" role="menuitem">Perfil</a>
                    </li>
                    <?php if($user->role == EUSER_TYPE::ADMIN)
                        {
                    ?>
                        <li>
                            <a href="/admin" class="block px-4 py-2 text-sm text-gray-300 hover:bg-gray-700 hover:text-white transition-colors duration-200" role="menuitem">Panel de administración</a>
                        </li>
                    <?php } ?>

                    <?php if(!is_null($user->getDeveloperInfo()))
                        {
                    ?>
                        <li>
                            <a href="/dev/panel" class="block px-4 py-2 text-sm text-gray-300 hover:bg-gray-700 hover:text-white transition-colors duration-200" role="menuitem">Panel de desarrollador</a>
                        </li>
                    <?php } ?>
                    
                    <li>
                        <a href="/logout" class="block px-4 py-2 text-sm text-gray-300 hover:bg-gray-700 hover:text-white transition-colors duration-200" role="menuitem">Cerrar sesión</a>
                    </li>
                </ul>

            <?php
        } else {
            ?>

                <button id="accountDropdown" class="account-dropdown flex items-center hover:text-gray-300 focus:outline-none" aria-haspopup="true" aria-expanded="false">
                    Cuenta
                    <svg xmlns="http://www.w3.org/2000/svg" class="dropdown-icon h-4 w-4 ml-1 transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
                <ul id="accountMenu" class="hidden absolute right-0 mt-2 py-2 w-48 bg-gray-800 rounded-md shadow-xl z-20" role="menu" aria-orientation="vertical" aria-labelledby="accountDropdown">
                    <li>
                        <a href="/login" class="block px-4 py-2 text-sm text-gray-300 hover:bg-gray-700 hover:text-white transition-colors duration-200" role="menuitem">Iniciar sesión</a>
                    </li>
                    <li>
                        <a href="/register" class="block px-4 py-2 text-sm text-gray-300 hover:bg-gray-700 hover:text-white transition-colors duration-200" role="menuitem">Unirse</a>
                    </li>
                </ul>

            <?php
        }
    }
}
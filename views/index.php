<!DOCTYPE html>
<html lang="es" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orion - Plataforma de Juegos</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        brand: '#1B2A49',
                    }
                }
            }
        }
    </script>
    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
        .animate-fadeIn {
            animation: fadeIn 0.3s ease-out;
        }
        .hover-pulse:hover {
            animation: pulse 0.3s ease-in-out;
        }
        .link-underline {
            position: relative;
        }
        .link-underline::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: -2px;
            left: 0;
            background-color: white;
            transition: width 0.3s ease;
        }
        .link-underline:hover::after {
            width: 100%;
        }
        .account-dropdown:hover .dropdown-icon,
        .account-dropdown[aria-expanded="true"] .dropdown-icon {
            transform: rotate(90deg);
        }
    </style>
</head>
<body class="bg-gray-900 text-white">
    <header class="bg-brand p-4">
        <nav class="container mx-auto flex justify-between items-center">
            <a href="/" class="text-2xl font-bold text-white flex items-center hover-pulse">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                </svg>
                <span>Orion</span>
            </a>
            <ul class="flex space-x-4 items-center">
                <li>
                    <a href="/" class="text-white hover:text-gray-300 link-underline">Inicio</a>
                </li>
                <li>
                    <a href="/juegos" class="text-white hover:text-gray-300 link-underline">Juegos</a>
                </li>
                <li>
                    <a href="/comunidad" class="text-white hover:text-gray-300 link-underline">Comunidad</a>
                </li>
                <li class="relative">
                    <button id="accountDropdown" class="account-dropdown text-white hover:text-gray-300 flex items-center focus:outline-none" aria-haspopup="true" aria-expanded="false">
                        Cuenta
                        <svg xmlns="http://www.w3.org/2000/svg" class="dropdown-icon h-4 w-4 ml-1 transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                    <ul id="accountMenu" class="absolute right-0 mt-2 py-2 w-48 bg-gray-800 rounded-md shadow-xl z-20 hidden animate-fadeIn" role="menu" aria-orientation="vertical" aria-labelledby="accountDropdown">
                        <li>
                            <a href="/iniciar-sesion" class="block px-4 py-2 text-sm text-gray-300 hover:bg-gray-700 hover:text-white transition-colors duration-200" role="menuitem">Iniciar sesión</a>
                        </li>
                        <li>
                            <a href="/unirse" class="block px-4 py-2 text-sm text-gray-300 hover:bg-gray-700 hover:text-white transition-colors duration-200" role="menuitem">Unirse</a>
                        </li>
                    </ul>
                </li>
            </ul>
        </nav>
    </header>

    <main class="container mx-auto mt-8 px-4">
        <h1 class="text-3xl font-bold mb-4">Bienvenido a Orion</h1>
        <p class="text-gray-300">Tu plataforma social de videojuegos.</p>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const accountDropdown = document.getElementById('accountDropdown');
            const accountMenu = document.getElementById('accountMenu');

            accountDropdown.addEventListener('click', function() {
                const expanded = this.getAttribute('aria-expanded') === 'true' || false;
                this.setAttribute('aria-expanded', !expanded);
                accountMenu.classList.toggle('hidden');
            });

            document.addEventListener('click', function(event) {
                if (!accountDropdown.contains(event.target) && !accountMenu.contains(event.target)) {
                    accountDropdown.setAttribute('aria-expanded', 'false');
                    accountMenu.classList.add('hidden');
                }
            });
        });
    </script>
</body>
</html>
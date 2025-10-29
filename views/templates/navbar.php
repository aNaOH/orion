<header id="navbar" class="bg-transparent fixed w-full p-4 transition-colors duration-300 z-50" style="top: 0; left: 0; right: 0;">
<nav class="container mx-auto flex justify-between items-center">
        <!-- Logo -->
        <a href="/" class="flex items-center">
            <img src="/assets/img/orion-logo.svg" alt="Orion logo" class="w-1/5 hover:w-1/4 transition-size duration-300">
        </a>

        <!-- Hamburger Menu (visible on small screens) -->
        <button id="menuButton" class="md:hidden text-white focus:outline-none">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>

        <!-- Navigation Links -->
        <ul id="menu" class="hidden md:flex space-x-4 items-center md:static absolute left-0 top-16 w-full md:w-auto bg-gray-800 md:bg-transparent text-white md:text-base text-lg shadow-lg md:shadow-none rounded-md md:rounded-none md:py-0 py-4 md:pl-0 pl-4">
            <li>
                <a href="/" class="hover:text-gray-300 link-underline">Inicio</a>
            </li>
            <li>
                <a href="/store" class="hover:text-gray-300 link-underline">Tienda</a>
            </li>
            <li>
                <a href="/communities" class="hover:text-gray-300 link-underline">Comunidad</a>
            </li>
            <li class="relative">
                <?php NavbarHelper::getUserNavbar($_SESSION["user"] ?? []); ?>
            </li>
            <?php if (isset($_SESSION["user"]) && OrderHelper::getOrder()) { ?>
                <li>
                    <a href="/store/cart">
                        <i class="bi bi-cart-fill"></i>
                    </a>
                </li>
            <?php } ?>
        </ul>
    </nav>
    </header>

<script>
        document.addEventListener('DOMContentLoaded', function() {

            const menuButton = document.getElementById('menuButton');
            const menu = document.getElementById('menu');
            const accountDropdown = document.getElementById('accountDropdown');
            const accountMenu = document.getElementById('accountMenu');
            const navbar = document.getElementById('navbar');

            // Toggle mobile menu
            menuButton.addEventListener('click', () => {
                menu.classList.toggle('hidden');
                menu.classList.toggle('menu-open');
            });

            // Toggle account dropdown
            accountDropdown.addEventListener('click', () => {
                const expanded = accountDropdown.getAttribute('aria-expanded') === 'true';
                accountDropdown.setAttribute('aria-expanded', !expanded);
                accountMenu.classList.toggle('hidden');
            });

            // Close dropdown when clicking outside
            document.addEventListener('click', (event) => {
                if (!accountDropdown.contains(event.target) && !accountMenu.contains(event.target)) {
                    accountDropdown.setAttribute('aria-expanded', 'false');
                    accountMenu.classList.add('hidden');
                }
            });

            const checkScroll = (navbar) => {
                if (window.scrollY > 10) { // Ajusta el valor según cuándo quieras que aparezca el fondo
                    navbar.classList.remove('bg-transparent');
                    navbar.classList.add('bg-branddark');
                } else {
                    navbar.classList.remove('bg-branddark');
                    navbar.classList.add('bg-transparent');
                }
            }

            // Change navbar background on scroll
            window.addEventListener('scroll', () => {
                checkScroll(navbar);
            });

            checkScroll(navbar);
        });
    </script>

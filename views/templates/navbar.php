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
            <li class="relative group">

                <!-- Botón del Carrito -->
                <a href="/store/cart" class="cursor-pointer flex items-center hover:text-gray-300">
                    <i class="bi bi-cart-fill text-lg"></i>
                    <span class="ml-2 block md:hidden">Ver carrito</span>
                </a>

                <!-- Dropdown carrito -->
                <div class="absolute right-0 mt-0 w-96 bg-gray-800 text-white rounded-md shadow-lg p-4
                            hidden md:group-hover:block md:hidden hover:block">

                    <!-- Mockup de productos -->
                    <ul class="space-y-2 text-sm">
                        <?php foreach (
                            OrderHelper::getInstances()
                            as $item
                        ) { ?>
                            <li class="flex items-center justify-between gap-2 py-1">

                                <!-- Imagen + título -->
                                <div class="flex items-center gap-2 min-w-0 flex-1">
                                    <img src="https://cdn.orion.moonnastd.com/game/icon/<?= $item->id ?>" alt="<?= $item->title ?>" class="w-6 h-6 rounded-md flex-shrink-0">
                                    <span class="truncate"><?= $item->title ?></span>
                                </div>

                                <!-- Precios -->
                                <div class="flex items-center gap-3 flex-shrink-0 text-right">
                                    <?php if ($item->discount > 0) { ?>

                                        <div class="flex flex-col leading-tight">
                                            <span class="text-green-400 text-xs font-semibold">-<?= $item->discount *
                                                100 ?>%</span>
                                            <span class="text-gray-400 line-through text-xs"><?= $item->base_price ?> €</span>
                                            <span class="text-gray-200 font-semibold"><?= round(
                                                $item->base_price -
                                                    $item->base_price *
                                                        $item->discount,
                                                2,
                                            ) ?> €</span>
                                        </div>

                                    <?php } else { ?>

                                        <span class="text-gray-200 font-semibold"><?= $item->base_price ?> €</span>

                                    <?php } ?>
                                </div>

                                <!-- Botón eliminar -->
                                <button data-id="<?= $item->id ?>" class="text-gray-400 hover:text-white transition duration-300 flex-shrink-0 delete-item">
                                    <i class="bi bi-trash-fill"></i>
                                </button>

                            </li>

                        <?php } ?>
                    </ul>

                    <hr class="my-3 border-gray-600">

                    <div class="flex justify-between">
                        <div class="flex items-center">
                            <span class="text-gray-400 mr-2">Total:</span>
                            <span class="text-gray-200 font-semibold"><?= OrderHelper::getTotal() ?> €</span>
                        </div>
                        <a href="/store/cart" class="text-center block bg-alt text-black font-semibold rounded py-2 px-4 hover:bg-alt-800 transition duration-300">
                            Ver carrito
                        </a>
                    </div>
                </div>

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

        // Delete item from cart
        document.querySelectorAll('.delete-item').forEach(button => {
            button.addEventListener('click', function() {
                const itemId = this.getAttribute('data-id');
                $.ajax({
                    url: `/api/cart/${itemId}`,
                    method: "DELETE",
                    success: function(response) {
                        location.reload();
                    },
                    error: function(error) {
                        console.error(error);
                    }
                });
            });
        });
    </script>

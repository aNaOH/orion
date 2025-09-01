<?php

$title = "Orion Store";

$GLOBALS['games'] = Game::all();

if (isset($_SESSION['user'])) {
    $user = User::getById($_SESSION['user']['id']);
    $GLOBALS['recommended'] = Recommender::getRecommendations($user);
}

$GLOBALS['randomGames'] = Game::pickRandom(4);

function showPage() {
    global $games;
    global $randomGames;
    global $recommended;

    ?>

   <!-- Hero Section -->
    <section id="hero" class="bg-brand-500 text-white py-20">
    <div class="container mx-auto text-center">
        <h2 class="text-4xl md:text-5xl font-bold animate__animated animate__fadeInDown">Tienda</h2>
        <p class="text-lg md:text-xl mt-4">¡Descubre tu próximo videojuego!</p>
    </div>

    <form id="searchForm" class="flex items-center space-x-2 p-2 rounded-full bg-branddark bg-opacity-75 shadow-md w-[50%] mx-auto">
                <!-- Campo de Búsqueda -->
                <div class="relative flex-grow">
                    <input 
                    type="text" 
                    name="search" 
                    id="search" 
                    value="<?= isset($searchQuery) ? htmlspecialchars($searchQuery) : ''; ?>"
                    placeholder="¡Mira si lo tenemos aquí!" 
                    class="w-full bg-transparent text-text-gray-200 placeholder-text-gray-200/75 px-4 py-2 rounded-full border-none focus:outline-none"
                    >
                    <!-- Icono de Búsqueda -->
                    <div class="absolute inset-y-0 right-3 flex items-center pointer-events-none">
                    <i class="bi bi-search text-white"></i>
                    </div>
                </div>
            </form>
    </section><!-- /Hero Section -->

    <?php if(isset($recommended) && count($recommended) > 0) { ?>
        <!-- Recommended Section -->
        <section id="features" class="py-5">
            <h2 class="text-2xl md:text-3xl font-semibold animate__animated animate__fadeInDown">Pensamos que podría interesarte</h2>

            <?php if(!isset($games) || count($games) == 0) { ?>
                <div class="container mx-auto text-center py-16 ">
                <h1 class="text-3xl md:text-4xl font-bold text-brand-800">No hay juegos...</h1>
                <p class="text-lg text-gray-600 mt-4">¡Vuelve pronto para descubrir nuevos títulos increíbles!</p>
                </div>
            <?php } else { ?>
                <div class="container mx-auto mt-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-8">
                        <?php foreach ($recommended as $game) {
                            if(!$game->is_public) continue;
                            OrionComponents::GameStore($game);
                        } ?>
                    </div>
                </div>
            <?php } ?>
        </section>
    <?php } ?>

    <section id="features" class="py-5">
        <h2 class="text-2xl md:text-3xl font-semibold animate__animated animate__fadeInDown">Algunos juegos de nuestro catálogo</h2>

    <?php if(!isset($games) || count($games) == 0) { ?>
        <div class="container mx-auto text-center py-16 ">
        <h1 class="text-3xl md:text-4xl font-bold text-brand-800">No hay juegos...</h1>
        <p class="text-lg text-gray-600 mt-4">¡Vuelve pronto para descubrir nuevos títulos increíbles!</p>
        </div>
    <?php } else { ?>
        <div class="container mx-auto mt-6">
            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-8">
                <?php foreach ($randomGames as $game) {
                    if(!$game->is_public) continue;
                    OrionComponents::GameStore($game);
                } ?>
            </div>
        </div>
    <?php } ?>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-5">
        <h2 class="text-2xl md:text-3xl font-semibold animate__animated animate__fadeInDown">Todos los juegos</h2>

    <?php if(!isset($games) || count($games) == 0) { ?>
        <div class="container mx-auto text-center py-16 ">
        <h1 class="text-3xl md:text-4xl font-bold text-brand-800">No hay juegos...</h1>
        <p class="text-lg text-gray-600 mt-4">¡Vuelve pronto para descubrir nuevos títulos increíbles!</p>
        </div>
    <?php } else { ?>
        <div class="container mx-auto mt-6">
            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-8">
                <?php foreach ($games as $game) {
                    if(!$game->is_public) continue;
                    OrionComponents::GameStore($game);
                } ?>
            </div>
        </div>
    <?php } ?>
    </section>

    <?php
}

include("views/templates/main.php");

unset($GLOBALS['games']);
unset($GLOBALS['randomGames']);
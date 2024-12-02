<?php

$title = "Comunidades en Orion";

$GLOBALS['games'] = Game::all();
$GLOBALS['randomGames'] = Game::pickRandom(5);

function showPage() {
    global $games;
    global $randomGames;

    ?>

   <!-- Hero Section -->
    <section id="hero" class="bg-brand-500 text-white py-20">
    <div class="container mx-auto text-center">
        <h2 class="text-4xl md:text-5xl font-bold animate__animated animate__fadeInDown">Tienda</h2>
        <p class="text-lg md:text-xl mt-4">Explora y únete a comunidades apasionadas por los videojuegos.</p>
    </div>
    </section><!-- /Hero Section -->

    <section id="features" class="py-5">
        <h2 class="text-2xl md:text-3xl font-semibold animate__animated animate__fadeInDown">Selección aleatoria</h2>

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
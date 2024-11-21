<?php

$title = "Comunidades en Orion";

$GLOBALS['games'] = Game::all();

function showPage() {
    global $games;
    ?>

   <!-- Hero Section -->
    <section id="hero" class="bg-brand-500 text-white py-20">
    <div class="container mx-auto text-center">
        <h2 class="text-4xl md:text-5xl font-bold animate__animated animate__fadeInDown">Comunidades</h2>
        <p class="text-lg md:text-xl mt-4">Explora y únete a comunidades apasionadas por los videojuegos.</p>
    </div>
    </section><!-- /Hero Section -->

    <!-- Features Section -->
    <section id="features" class="py-5">
    <?php if(!isset($games) || count($games) == 0) { ?>
        <div class="container mx-auto text-center py-16">
        <h1 class="text-3xl md:text-4xl font-bold text-brand-800">No hay juegos...</h1>
        <p class="text-lg text-gray-600 mt-4">¡Vuelve pronto para descubrir nuevos títulos increíbles!</p>
        </div>
    <?php } else { ?>
        <div class="container mx-auto">
            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-8">
                <?php foreach ($games as $game) { ?>
                    <?php OrionComponents::GameCommunity($game); ?>
                <?php } ?>
            </div>
        </div>
    <?php } ?>
    </section>

    <?php
}

include("views/templates/main.php");

unset($GLOBALS['games']);
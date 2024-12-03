<?php

$title = "Tu biblioteca en Orion";

function showPage() {
    global $games;
    ?>

   <!-- Hero Section -->
    <section id="hero" class="bg-brand-500 text-white py-20">
    <div class="container mx-auto text-center">
        <h2 class="text-4xl md:text-5xl font-bold animate__animated animate__fadeInDown">Biblioteca</h2>
    </div>
    </section><!-- /Hero Section -->

    <!-- Features Section -->
    <section id="features" class="py-5">
    <?php if(!isset($games) || count($games) == 0) { ?>
        <div class="container mx-auto text-center py-16">
        <h1 class="text-3xl md:text-4xl font-bold text-brand-800">No hay juegos...</h1>
        <p class="text-lg text-gray-600 mt-4">¡Ve a la <a href="/store" class="hover:text-gray-700">tienda</a> para descubrir nuevos títulos increíbles!</p>
        </div>
    <?php } else { ?>
        <div class="container mx-auto">
            <div class="flex flex-col gap-8">
                <?php foreach ($games as $game) {
                    OrionComponents::GameLibrary($game);
                } ?>
            </div>
        </div>
    <?php } ?>
    </section>

    <?php
}

include("views/templates/main.php");

unset($GLOBALS['games']);
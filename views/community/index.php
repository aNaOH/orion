<?php

$title = "Comunidad de $game->title en Orion";

function showPage() {
    global $game;
    ?>

    <!-- Hero Section -->
    <section id="hero" class="bg-brand-500 text-white py-20">
    <div class="container mx-auto text-center">
        <h2 class="text-4xl md:text-5xl font-bold animate__animated animate__fadeInDown"><?= $game->title ?></h2>
        <p class="text-lg md:text-xl mt-4">Explora y contribuye al contenido creado por la comunidad de este juego.</p>
        <div class="flex justify-center gap-6 mt-6">
        <a href="/communities/<?= $game->id ?>/posts" class="px-6 py-3 bg-alt text-white font-semibold rounded-lg shadow-md hover:bg-alt-400 transition animate__animated animate__fadeInUp">
            Posts
        </a>
        <a href="/communities/<?= $game->id ?>/gallery" class="px-6 py-3 bg-alt text-white font-semibold rounded-lg shadow-md hover:bg-alt-400 transition animate__animated animate__fadeInUp">
            Galería
        </a>
        <a href="/communities/<?= $game->id ?>/guides" class="px-6 py-3 bg-alt text-white font-semibold rounded-lg shadow-md hover:bg-alt-400 transition animate__animated animate__fadeInUp">
            Guías
        </a>
        </div>
    </div>
    </section><!-- /Hero Section -->

    <?php
}

include("views/templates/main.php");

unset($GLOBALS['game']);
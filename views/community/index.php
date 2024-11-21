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

    <!-- Features Section -->
    <section id="features" class="py-20">
    <div class="container mx-auto">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-12">
        <!-- Últimos posts -->
        <div class="bg-brand-600 shadow-lg rounded-lg p-6">
            <div class="text-center mb-4">
            <h3 class="text-2xl text-gray-200 font-semibold text-brand-500">Últimos posts</h3>
            <p class="text-gray-200 text-sm">Descubre lo que la comunidad está compartiendo sobre este juego.</p>
            </div>
            <div class="space-y-2">
            <!-- Aquí puedes agregar dinámicamente los posts -->
            <p class="text-gray-200">- Post 1: Descripción breve...</p>
            <p class="text-gray-200">- Post 2: Descripción breve...</p>
            </div>
            <div class="text-center mt-4">
            <a href="/communities/<?= $game->id ?>/posts" class="text-alt-500 font-medium hover:text-alt-700 transition">
                Ver más...
            </a>
            </div>
        </div>

        <!-- Galería -->
        <div class="bg-brand-600 shadow-lg rounded-lg p-6">
            <div class="text-center mb-4">
            <h3 class="text-2xl text-gray-200 font-semibold text-brand-500">Galería</h3>
            <p class="text-gray-200 text-sm">Explora imágenes y contenido visual creado por la comunidad.</p>
            </div>
            <div class="grid grid-cols-2 gap-2">
            <!-- Aquí puedes agregar imágenes dinámicas -->
            <img src="/path-to-image1.jpg" alt="Imagen 1" class="rounded-lg shadow-sm">
            <img src="/path-to-image2.jpg" alt="Imagen 2" class="rounded-lg shadow-sm">
            </div>
            <div class="text-center mt-4">
            <a href="/communities/<?= $game->id ?>/gallery" class="text-alt-500 font-medium hover:text-alt-700 transition">
                Ver más...
            </a>
            </div>
        </div>

        <!-- Últimas guías -->
        <div class="bg-brand-600 shadow-lg rounded-lg p-6">
            <div class="text-center mb-4">
            <h3 class="text-2xl text-gray-200 font-semibold text-brand-500">Últimas guías</h3>
            <p class="text-gray-200 text-sm">Consulta las mejores estrategias y tutoriales creados por jugadores.</p>
            </div>
            <div class="space-y-2">
            <!-- Aquí puedes agregar dinámicamente las guías -->
            <p class="text-gray-200">- Guía 1: Descripción breve...</p>
            <p class="text-gray-200">- Guía 2: Descripción breve...</p>
            </div>
            <div class="text-center mt-4">
            <a href="/communities/<?= $game->id ?>/guides" class="text-alt-500 font-medium hover:text-alt-700 transition">
                Ver más...
            </a>
            </div>
        </div>
        </div>
    </div>
    </section><!-- /Features Section -->



    <?php
}

include("views/templates/main.php");

unset($GLOBALS['game']);
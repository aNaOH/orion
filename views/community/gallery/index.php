<?php

$title = "Posts de $game->title en Orion";

function showPage() {
    global $game;
    global $posts;
    ?>

    <!-- Hero Section -->
    <section id="hero" class="bg-brand-500 text-white py-20">
    <div class="container mx-auto text-center">
        <h2 class="text-4xl md:text-5xl font-bold animate__animated animate__fadeInDown">Galería de <?= $game->title ?></h2>
        <?php if (isset($_SESSION['user']['id'])) { ?>
        <div class="mt-6">
            <a href="/communities/<?= $game->id ?>/gallery/create" class="px-6 py-3 bg-alt text-white font-semibold rounded-lg shadow-md hover:bg-alt-400 transition animate__animated animate__fadeInUp">
            Nuevo post
            </a>
        </div>
        <?php } ?>
    </div>
    </section><!-- /Hero Section -->

    <!-- Features Section -->
    <section id="features" class="py-20">
    <div class="container mx-auto">
        <div class="space-y-4">

        <div class="max-w-xl mx-auto bg-white rounded-lg shadow-md overflow-hidden">
            <!-- Imagen del post -->
            <img 
            src="https://placehold.co/600x300" 
            alt="Post image" 
            class="w-full h-48 object-cover"
            />

            <!-- Contenido del post -->
            <div class="p-4">
            <h1 class="text-2xl font-bold text-gray-800">
                Título del Post
            </h1>
            <p class="text-gray-600 mt-2">
                Este es un ejemplo de un post estilo Reddit. Puedes votar utilizando el widget interactivo.
            </p>
            </div>

            <!-- Componente de voto -->
            <div class="flex items-center justify-between p-4 border-t">
            <span class="text-gray-600 text-sm">Publicado por u/usuario123</span>
            <gallery-vote></gallery-vote>
            </div>
        </div>

        <?php foreach ($posts as $post) { 
            if (!$post->is_public) continue;
        ?>
            
        <?php } ?>
        </div>
    </div>
    </section><!-- /Features Section -->

    <script src="/assets/js/addDatesCommunity.js"></script>
    <script src="/assets/js/components/galleryvote.js"></script>
    <?php
}

include("views/templates/main.php");

unset($GLOBALS['game']);
unset($GLOBALS['posts']);
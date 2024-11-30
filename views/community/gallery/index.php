<?php

$title = "Posts de $game->title en Orion";

function showPage() {
    global $game;
    global $posts;

    OrionComponents::TokenInput(ETOKEN_TYPE::USERACTION);
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

        <?php foreach ($posts as $post) { 
            if (!$post->is_public) continue;
        
            OrionComponents::GalleryEntry($post);
        } ?>
        </div>
    </div>
    </section><!-- /Features Section -->

    <script src="/assets/js/addDatesCommunity.js"></script>
    <script src="/assets/js/components/galleryvote.js"></script>
    <script src="/assets/js/galleryIndex.js"></script>
    
    <?php
}

include("views/templates/main.php");

unset($GLOBALS['game']);
unset($GLOBALS['posts']);
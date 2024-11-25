<?php

$title = "Guías pata $game->title en Orion";

function showPage() {
    global $game;
    global $posts;
    ?>

    <!-- Hero Section -->
    <section id="hero" class="bg-brand-500 text-white py-20">
    <div class="container mx-auto text-center">
        <h2 class="text-4xl md:text-5xl font-bold animate__animated animate__fadeInDown">Guías para <?= $game->title ?></h2>
        <?php if (isset($_SESSION['user']['id'])) { ?>
        <div class="mt-6">
            <a href="/communities/<?= $game->id ?>/guides/create" class="px-6 py-3 bg-alt text-white font-semibold rounded-lg shadow-md hover:bg-alt-400 transition animate__animated animate__fadeInUp">
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
        ?>
            <!-- Post Item -->
            <a href="/communities/<?= $game->id ?>/guides/<?= $post->id ?>" class="block bg-branddark shadow-lg rounded-lg p-6 hover:bg-branddark-600 transition-colors duration-300">
            <div class="flex justify-between items-center">
                <div>
                <h6 class="text-lg font-semibold text-gray-200 mb-1"><?= $post->title ?></h6>
                <p class="text-sm text-gray-300">de <?= $post->getAuthor()->username ?></p>
                </div>
                <small class="text-gray-400 text-sm" data-createdate="<?= $post->created_at->format('Y-m-d H:i:s') ?>">
                <?= $post->created_at->format('d/m/Y') ?>
                </small>
            </div>
            </a>
        <?php } ?>
        </div>
    </div>
    </section><!-- /Features Section -->


    <script src="/assets/js/addDatesCommunity.js"></script>

    <?php
}

include("views/templates/main.php");

unset($GLOBALS['game']);
unset($GLOBALS['posts']);
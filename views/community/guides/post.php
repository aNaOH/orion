<?php

$title = "Post '$post->title' para $game->title en Orion";

function showPage() {
    global $game;
    global $post;
    ?>

    <!-- Hero Section -->
    <section id="hero" class="bg-brand-500 text-white py-20">
    <div class="container mx-auto text-center">
        <h2 class="text-4xl md:text-5xl font-bold animate__animated animate__fadeInDown">
        <?= $post->title ?> 
        </h2>
        <p class="text-lg mt-4">
        Escrito por <span class="font-semibold"><?= $post->getAuthor()->username ?></span>
        </p>
    </div>
    </section><!-- /Hero Section -->

    <!-- Features Section -->
    <section id="features" class="py-20">
    <div class="container mx-auto p-6">
        <?php
        $Parsedown = new TailwindParsedown();
        echo $Parsedown->text($post->body);
        ?>
    </div>
    </section><!-- /Features Section -->

    <?php
}

include("views/templates/main.php");

unset($GLOBALS['game']);
unset($GLOBALS['post']);
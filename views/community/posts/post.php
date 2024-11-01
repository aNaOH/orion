<?php

$title = "Post '$post->title' para $game->title en Orion";

function showPage() {
    global $game;
    global $post;
    ?>

    <!-- Hero Section -->
    <section id="hero" class="hero section dark-background">

        <div class="carousel-container">
            <h2 class="animate__animated animate__fadeInDown">Posts de <?= $game->title ?></h2>
          </div>

    </section><!-- /Hero Section -->

    <!-- Features Section -->
    <section id="features" class="features section">

    

    </section><!-- /Features Section -->

    <?php
}

include("views/templates/main.php");

unset($GLOBALS['game']);
unset($GLOBALS['post']);
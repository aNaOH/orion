<?php

$title = "Post '$post->title' para $game->title en Orion";

function showPage() {
    global $game;
    global $post;
    ?>

    <!-- Hero Section -->
    <section id="hero" class="hero section dark-background">

        <div class="carousel-container">
            <h2 class="animate__animated animate__fadeInDown"><?= $post->title ?> de <?= $post->getAuthor()->username ?></h2>
          </div>

    </section><!-- /Hero Section -->

    <!-- Features Section -->
    <section id="features" class="features section">

        <div class="container">
        <?php
            $Parsedown = new Parsedown();
            echo $Parsedown->text($post->body);
        ?>
        </div>

    </section><!-- /Features Section -->

    <?php
}

include("views/templates/main.php");

unset($GLOBALS['game']);
unset($GLOBALS['post']);
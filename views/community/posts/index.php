<?php

$title = "Posts de $game->title en Orion";

function showPage() {
    global $game;
    ?>

    <!-- Hero Section -->
    <section id="hero" class="hero section dark-background">

        <div class="carousel-container">
            <h2 class="animate__animated animate__fadeInDown">Posts de <?= $game->title ?></h2>
            <?php if (isset($_SESSION['user']['id'])) { ?>
                <div class="d-flex flex-row justify-content-between">
                    <a href="/communities/<?= $game->id ?>/posts/create" class="btn-get-started animate__animated animate__fadeInUp scrollto">Nuevo post</a>
                </div>
            <?php } ?>
          </div>

    </section><!-- /Hero Section -->

    <!-- Features Section -->
    <section id="features" class="features section">

    

    </section><!-- /Features Section -->

    <?php
}

include("views/templates/main.php");

unset($GLOBALS['game']);
unset($GLOBALS['posts']);
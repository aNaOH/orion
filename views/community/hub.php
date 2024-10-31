<?php

$title = "Comunidades en Orion";

function showPage() {
    global $games;
    ?>

    <script src="/assets/js/components/game-community.js"></script>

    <!-- Hero Section -->
    <section id="hero" class="hero section dark-background">

        <div class="carousel-container">
            <h2 class="animate__animated animate__fadeInDown">Comunidades</h2>
          </div>

    </section><!-- /Hero Section -->

    <!-- Features Section -->
    <section id="features" class="features section">

        <?php if(!isset($games) || count($games) == 0) { ?>

            <div class="container">
                <h1>No hay juegos...</h1>
            </div>

        <?php } else { ?>
        <div class="container md-3">

            <?php foreach ($games as $game) { ?>
               
                <game-community game-title="<?= $game->title ?>" game-id="<?= $game->id ?>"/>

            <?php } ?>

        </div>

        <?php } ?>

    </section><!-- /Features Section -->

    <?php
}

include("views/templates/main.php");
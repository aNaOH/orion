<?php

$title = "Posts de $game->title en Orion";

function showPage() {
    global $game;
    ?>

    <!-- Hero Section -->
    <section id="hero" class="hero section dark-background">

        <div class="carousel-container">
            <h2 class="animate__animated animate__fadeInDown">Crear post para <?= $game->title ?></h2>
          </div>

    </section><!-- /Hero Section -->

    <!-- Features Section -->
    <section id="features" class="features section">

        <form action="" method="post">
            <?php OrionComponents::TokenInput(ETOKEN_TYPE::USERACTION) ?>
        </form>

    </section><!-- /Features Section -->

    <?php
}

include("views/templates/main.php");

unset($GLOBALS['game']);
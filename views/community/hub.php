<?php

$title = "Comunidades en Orion";

$GLOBALS['games'] = Game::all();

function showPage() {
    global $games;
    ?>

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
        <div class="container lg-5 md-3 sm-2">
            <div class="row">

                <?php foreach ($games as $game) {
                
                    OrionComponents::GameCommunity($game);

                } ?>

            </div>
        </div>

        <?php } ?>

    </section><!-- /Features Section -->

    <?php
}

include("views/templates/main.php");

unset($GLOBALS['games']);
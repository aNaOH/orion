<?php

$title = $game->title." | Comunidad";

function showPage() {
    global $game;
    ?>

    <!-- Hero Section -->
    <section id="hero" class="hero section dark-background">

        <div class="carousel-container">
            <h2 class="animate__animated animate__fadeInDown"><?=$game->title?></h2>
            <p class="animate__animated animate__fadeInUp">Lo que buscas no está aquí... Quizas ya no exista.</p>
            <a href="/" class="btn-get-started animate__animated animate__fadeInUp scrollto">Llevame de vuelta</a>
          </div>

    </section><!-- /Hero Section -->

    <?php
}

include("views/templates/main.php");
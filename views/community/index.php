<?php

$title = "Comunidad de $game->title en Orion";

function showPage() {
    global $game;
    ?>

    <!-- Hero Section -->
    <section id="hero" class="hero section dark-background">

        <div class="carousel-container">
            <h2 class="animate__animated animate__fadeInDown"><?= $game->title ?></h2>
            <div class="d-flex flex-row justify-content-between">
                <a href="/communities/<?= $game->id ?>/posts" class="btn-get-started animate__animated animate__fadeInUp scrollto">Posts</a>
                <a href="/communities/<?= $game->id ?>/gallery" class="btn-get-started animate__animated animate__fadeInUp">Galería</a>
                <a href="/communities/<?= $game->id ?>/guides" class="btn-get-started animate__animated animate__fadeInUp">Guías</a>
            </div>
          </div>

    </section><!-- /Hero Section -->

    <!-- Features Section -->
    <section id="features" class="features section">

    <div class="container">
        <div class="row justify-content-around w-100">
            <div class="col-3">
                <h3 class="text-center">Últimos posts</h3>
                <!-- Añadir aquí los posts -->
                 <a href="/communities/<?= $game->id ?>/posts">Ver más...</a>
            </div>
            <div class="col-3">
                <h3 class="text-center">Galería</h3>
                <!-- Añadir aquí los posts -->
                <a href="/communities/<?= $game->id ?>/gallery">Ver más...</a>
            </div>
            <div class="col-3">
                <h3 class="text-center">Últimas guías</h3>
                <!-- Añadir aquí los posts -->
                <a href="/communities/<?= $game->id ?>/guides">Ver más...</a>
            </div>
        </div>
    </div>

    </section><!-- /Features Section -->

    <?php
}

include("views/templates/main.php");

unset($GLOBALS['game']);
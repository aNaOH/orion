<?php

$title = "Subir en la galería de $game->title en Orion";

function showPage() {
    global $game;
    ?>
    <script src="/assets/js/components/fileUpload.js"></script>

    <!-- Hero Section -->
    <section id="hero" class="bg-brand-500 text-white pt-20">
    <div class="container mx-auto text-center">
        <h2 class="text-2xl md:text-4xl font-bold animate__animated animate__fadeInDown">Subir imagen para <?= $game->title ?></h2>
    </div>
    </section><!-- /Hero Section -->

    <!-- Features Section -->
    <section id="features" class="py-20">
    <div class="container mx-auto max-w-2xl">
        <form id="communityCreateForm" novalidate class="bg-branddark shadow-lg rounded-lg p-8 space-y-6">
        <?php OrionComponents::TokenInput(ETOKEN_TYPE::USERACTION) ?>

        <!-- Título -->
        <div>
            <label for="title" class="block text-sm font-medium text-gray-200">Título</label>
            <input class="form-control block w-full p-4 border border-gray-300 rounded-lg shadow-sm focus:border-brand-500 focus:ring focus:ring-brand-200"
                id="title" name="title" type="text" placeholder="Título" required />
            <div id="titleError" class="text-sm text-red-500 mt-2 hidden"></div>
        </div>

        <!-- Cuerpo -->
        <div>
            <label for="body" class="block text-sm font-medium text-gray-200">Contenido del post</label>
            <div class="p-2 ">
                <file-upload id="body" accept-video="true" max-video-size="15MB"></file-upload>
            </div>
        </div>

        <!-- Botón Publicar -->
        <div class="text-right">
            <button class="px-6 py-3 bg-alt-500 text-white font-semibold rounded-lg shadow-lg hover:bg-alt-400 focus:ring focus:ring-brand-300 transition" id="submitButton" type="submit">
            Publicar post
            </button>
        </div>
        </form>
    </div>
    </section><!-- /Features Section -->

    <script src="/assets/js/forms/gallery-create.js"></script>

    <?php
}

include("views/templates/main.php");

unset($GLOBALS['game']);
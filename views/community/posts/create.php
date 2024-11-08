<?php

$title = "Posts de $game->title en Orion";

function showPage() {
    global $game;
    ?>
    <link rel="stylesheet" href="/assets/vendor/simplemde/simplemde.min.css">
    <script src="/assets/vendor/simplemde/simplemde.min.js"></script>

    <!-- Hero Section -->
    <section id="hero" class="hero section dark-background">

        <div class="carousel-container">
            <h2 class="animate__animated animate__fadeInDown">Crear post para <?= $game->title ?></h2>
          </div>

    </section><!-- /Hero Section -->

    <!-- Features Section -->
    <section id="features" class="features section">

        <div class="container">
            <form id="communityCreateForm" novalidate>
                <?php OrionComponents::TokenInput(ETOKEN_TYPE::USERACTION) ?>

                <div class="form-floating mb-3">
                    <input class="form-control" id="title" name="title" type="text" placeholder="Título" required />
                    <label for="title">Título</label>
                    <div id="titleError" class="invalid-feedback"></div>
                </div>

                <textarea name="body" id="body" class="m-2"></textarea>

                <button class="btn btn-primary btn-lg" id="submitButton" type="submit">Publicar post</button>
            </form>
        </div>

    </section><!-- /Features Section -->

    <script>
        var simplemde = new SimpleMDE({ 
            element: document.getElementById("body"),
            autosave: {
                enabled: true,
                uniqueId: "Orion_NewPost_Body",
                delay: 1000,
            },
            insertTexts: {
                horizontalRule: ["", "\n\n-----\n\n"],
                image: ["![](http://", ")"],
                link: ["[", "](http://)"],
                table: ["", "\n\n| Column 1 | Column 2 | Column 3 |\n| -------- | -------- | -------- |\n| Text     | Text      | Text     |\n\n"],
            },
            placeholder: "Type here...",
            hideIcons: ["side-by-side", "fullscreen"],
         });
    </script>

    <script src="/assets/js/forms/community-create.js"></script>

    <?php
}

include("views/templates/main.php");

unset($GLOBALS['game']);
<?php

$title = "Posts de $game->title en Orion";

function showPage()
{
    global $game;
    global $guideTypes;
    ?>
    <link rel="stylesheet" href="/assets/vendor/simplemde/simplemde.min.css">
    <script src="/assets/vendor/simplemde/simplemde.min.js"></script>

    <script src="/assets/js/components/gradientSquare.js"></script>

    <!-- Hero Section -->
    <section id="hero" class="bg-brand-500 text-white pt-20">
    <div class="container mx-auto text-center">
        <h2 class="text-2xl md:text-4xl font-bold animate__animated animate__fadeInDown">Crear guía para <?= $game->title ?></h2>
    </div>
    </section><!-- /Hero Section -->

    <!-- Features Section -->
    <section id="features" class="py-20">
    <div class="container mx-auto max-w-2xl">
        <form id="communityCreateForm" novalidate class="bg-branddark shadow-lg rounded-lg p-8 space-y-6">
        <?php OrionComponents::TokenInput(ETOKEN_TYPE::USERACTION); ?>

        <!-- Título -->
        <div>
            <label for="title" class="block text-sm font-medium text-gray-200">Título</label>
            <input class="form-control block w-full p-4 border border-gray-300 rounded-lg shadow-sm focus:border-brand-500 focus:ring focus:ring-brand-200 text-brand"
                id="title" name="title" type="text" placeholder="Título" required />
            <div id="titleError" class="text-sm text-red-500 mt-2 hidden"></div>
        </div>

        <!-- Tipo -->
        <div>
            <label for="guideType" class="block text-sm font-medium text-gray-200">Seleccionar tipo</label>
            <div class="p-2 bg-white rounded-lg shadow-lg flex flex-row gap-x-2 content-center transition-colors duration-200" style="background-color: <?= $guideTypes[0]
                ->tint ?>;" id="guideContainer">
                <div class="shadow-2xl">
                    <gradient-square id="guideIcon" size="55" base-color="<?= $guideTypes[0]
                        ->tint ?>" icon-path="https://cdn.orion.moonnastd.com/guidetype/<?= $guideTypes[0]
    ->icon ?>" ></gradient-square>
                </div>
                <select name="guideType" id="guideType" class="text-brand form-control block w-full p-4 border border-gray-300 rounded-lg shadow-sm focus:border-brand-500 focus:ring focus:ring-brand-200">
                    <?php if (isset($guideTypes) && count($guideTypes) > 0) {
                        foreach ($guideTypes as $gType) { ?>
                        <option <?php if ($guideTypes[0] == $gType) {
                            echo "selected";
                        } ?> value="<?= $gType->id ?>" data-color="<?= $gType->tint ?>" data-icon="https://cdn.orion.moonnastd.com/guidetype/<?= $gType->icon ?>"><?= $gType->type ?></option>
                    <?php };
                    } ?>
                </select>
            </div>
        </div>

        <!-- Cuerpo -->
        <div>
            <label for="body" class="block text-sm font-medium text-gray-200">Contenido de la guía</label>
            <div class="p-2 bg-white rounded-lg shadow-lg">
                <textarea name="body" id="body" class="w-full p-4 border border-gray-300 rounded-lg shadow-sm focus:border-brand-500 focus:ring focus:ring-brand-200" rows="8" placeholder="Escribe el contenido aquí..." required></textarea>
            </div>
        </div>

        <!-- Botón Publicar -->
        <div class="text-right">
            <button class="px-6 py-3 bg-alt-500 text-white font-semibold rounded-lg shadow-lg hover:bg-alt-400 focus:ring focus:ring-brand-300 transition" id="submitButton" type="submit">
            Publicar guía
            </button>
        </div>
        </form>
    </div>
    </section><!-- /Features Section -->

    <script>
        var simplemde = new SimpleMDE({
            element: document.getElementById("body"),
            autosave: {
                enabled: true,
                uniqueId: "Orion_NewGuide_Body",
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

    <script src="/assets/js/helpers/colorHelper.js"></script>

    <script src="/assets/js/forms/community-create.js"></script>
    <script src="/assets/js/forms/guide-create.js"></script>

    <?php
}

include "views/templates/main.php";

unset($GLOBALS["game"]);
if (isset($GLOBALS["guideTypes"])) {
    unset($GLOBALS["guideTypes"]);
}

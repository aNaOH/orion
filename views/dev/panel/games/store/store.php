<?php

$title = "Editar tienda para " . $game->title . " | Orion Dev Panel";

function showPage()
{
    global $game;
    global $features;
    global $genres;
    ?>

<link rel="stylesheet" href="/assets/vendor/simplemde/simplemde.orion.css">
<script src="/assets/vendor/simplemde/simplemde.min.js"></script>

<script src="/assets/js/components/gradientChip.js"></script>
<script src="/assets/js/components/fileUpload.js"></script>

<div class="container mx-auto px-6 mt-6 max-w-full">
    <div class="flex justify-between items-center">
        <h1 class="text-xl font-semibold text-alt">Editar tienda para <?= $game->title ?></h1>
        <a href="/dev/panel/games/" class="text-gray-400 hover:text-alt transition">Volver</a>
    </div>
  <!-- Tabs -->
  <div class="border-b border-gray-700">
    <nav class="flex space-x-6" id="tabButtons">
      <button
        data-tab="store"
        class="tab-button text-alt border-b-2 border-alt py-2 font-medium transition"
      >
        Página de la tienda
      </button>
      <button
        data-tab="builds"
        class="tab-button text-gray-400 hover:text-alt border-b-2 border-transparent py-2 font-medium transition"
      >
        Compilaciones
      </button>
    </nav>
  </div>

  <!-- Contenido de las pestañas -->
  <div id="tabContent" class="mt-6">
    <!-- Pestaña Tienda -->
    <div id="store" class="tab-pane block">
      <form id="editGameForm" class="space-y-6">
        <!-- Título -->
        <div class="relative">
          <input
            type="text"
            id="title"
            name="title"
            placeholder=" "
            value="<?= $game->title ?>"
            class="peer w-full bg-[#0f172a] border border-gray-600 text-gray-200 rounded-lg px-4 pt-6 pb-2 focus:ring-2 focus:ring-alt focus:border-alt outline-none placeholder-transparent"
          />
          <label
            for="title"
            class="absolute left-4 top-3 text-gray-400 text-sm transition-all
                   peer-placeholder-shown:top-4 peer-placeholder-shown:text-base peer-placeholder-shown:text-gray-500
                   peer-focus:top-2.5 peer-focus:text-sm peer-focus:text-alt"
          >
            Título
          </label>
        </div>

        <!-- Descripción corta -->
        <div class="relative">
          <input
            type="text"
            id="shortDescription"
            name="shortDescription"
            placeholder=" "
            value="<?= $game->short_description ?>"
            class="peer w-full bg-[#0f172a] border border-gray-600 text-gray-200 rounded-lg px-4 pt-6 pb-2 focus:ring-2 focus:ring-alt focus:border-alt outline-none placeholder-transparent"
          />
          <label
            for="shortDescription"
            class="absolute left-4 top-3 text-gray-400 text-sm transition-all
                   peer-placeholder-shown:top-4 peer-placeholder-shown:text-base peer-placeholder-shown:text-gray-500
                   peer-focus:top-2.5 peer-focus:text-sm peer-focus:text-alt"
          >
            Descripción corta
          </label>
        </div>

        <!-- Género -->
        <div>
          <label for="genre" class="block mb-2 text-gray-300 font-medium">Género</label>
          <select
            id="genre"
            name="genre"
            class="w-full bg-[#0f172a] border border-gray-600 text-gray-200 rounded-lg px-4 py-3 focus:ring-2 focus:ring-alt focus:border-alt outline-none"
          >
            <?php foreach ($genres as $genre): ?>
              <option value="<?= $genre->id ?>" <?= $game->genre_id ==
$genre->id
    ? "selected"
    : "" ?>>
                <?= $genre->name ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <!-- Características -->
        <div class="flex flex-col gap-2">
          <p class="block mb-2 text-gray-300 font-medium">Características</p>

          <div class="flex flex-row gap-2">
              <select
                id="featureSelector"
                class="w-full bg-[#0f172a] border border-gray-600 text-gray-200 rounded-lg px-4 py-3 focus:ring-2 focus:ring-alt focus:border-alt outline-none"
              >
                <option value="">Seleccionar característica</option>
                <?php foreach ($features as $feature): ?>
                  <option value="<?= $feature->id ?>">
                    <?= $feature->name ?>
                  </option>
                <?php endforeach; ?>
              </select>
              <button type="button" id="addFeatureButton" class="flex justify-center items-center bg-alt text-[#1B2A49] font-medium px-6 py-3 rounded-lg hover:opacity-90 transition w-[25%]">Agregar característica</button>
          </div>
          <div id="featuresContainer" class="flex flex-col gap-2">
              <?php foreach ($game->getFeatures() as $gameFeature): ?>
                <div class="flex items-center gap-2" data-feature-id="<?= $gameFeature->id ?>">
                  <gradient-chip
                        base-color="<?= $gameFeature->tint ?>"
                        size="24"
                        icon-path="https://cdn.orion.moonnastd.com/game/feature/<?= $gameFeature->icon ?>"
                        text="<?= $gameFeature->name ?>"
                        border-radius="8"
                        class="w-full">
                  </gradient-chip>
                  <button type="button" class="bg-red-500 text-white rounded-full px-2 py-1 hover:bg-red-600" data-feature-id="<?= $gameFeature->id ?>" onclick="deleteFeature(<?= $gameFeature->id ?>)">Eliminar</button>
                </div>
              <?php endforeach; ?>
          </div>
        </div>

        <!-- Checkbox -->
        <div class="flex items-center gap-2">
          <input
            id="asEditor"
            name="asEditor"
            type="checkbox"
            <?= $game->as_editor ? "checked" : "" ?>
            class="w-4 h-4 accent-alt focus:ring-alt"
          />
          <label for="asEditor" class="text-gray-300 font-medium">¿Eres la editora?</label>
        </div>

        <!-- Desarrollador -->
        <div id="developerNameContainer" class="<?= $game->as_editor
            ? ""
            : "hidden" ?> relative">
          <input
            type="text"
            id="developerName"
            name="developerName"
            placeholder=" "
            value="<?= $game->developer_name ?>"
            class="peer w-full bg-[#0f172a] border border-gray-600 text-gray-200 rounded-lg px-4 pt-6 pb-2 focus:ring-2 focus:ring-alt focus:border-alt outline-none placeholder-transparent"
          />
          <label
            for="developerName"
            class="absolute left-4 top-3 text-gray-400 text-sm transition-all
                   peer-placeholder-shown:top-4 peer-placeholder-shown:text-base peer-placeholder-shown:text-gray-500
                   peer-focus:top-2.5 peer-focus:text-sm peer-focus:text-alt"
          >
            Desarrollador
          </label>
        </div>

        <!-- Precio -->
        <div>
          <label for="price" class="block mb-2 text-gray-300 font-medium">Precio</label>
          <select
            id="price"
            name="price"
            class="w-full bg-[#0f172a] border border-gray-600 text-gray-200 rounded-lg px-4 py-3 focus:ring-2 focus:ring-alt focus:border-alt outline-none"
          >
            <option value="0" <?= is_null($game->base_price) ||
            $game->base_price == 0
                ? "selected"
                : "" ?>>Gratis</option>
            <?php
            $prices = [
                1.99,
                2.99,
                3.99,
                4.99,
                5.99,
                6.99,
                7.99,
                8.99,
                9.99,
                14.99,
                19.99,
                24.99,
                29.99,
                39.99,
                49.99,
                59.99,
                69.99,
                79.99,
            ];
            foreach ($prices as $price) {
                echo "<option value='{$price}' " .
                    ($game->base_price == $price ? "selected" : "") .
                    ">" .
                    str_replace(".", ",", $price) .
                    " €</option>";
            }
            ?>
          </select>
        </div>

        <!-- Descuento -->
        <div>
          <label for="discount" class="block mb-2 text-gray-300 font-medium">Descuento</label>
          <div class="flex items-center gap-2">
            <input
              id="discount"
              name="discount"
              type="number"
              min="0"
              max="100"
              step="1"
              value="<?= $game->discount * 100 ?>"
              class="w-full bg-[#0f172a] border border-gray-600 text-gray-200 rounded-lg px-4 py-3 focus:ring-2 focus:ring-alt focus:border-alt outline-none"
            />
            <span class="text-gray-400 font-medium">%</span>
          </div>
        </div>

        <!-- Descripción -->
        <div>
          <label for="description" class="block mb-2 text-gray-300 font-medium">Descripción</label>
          <textarea
            id="description"
            name="description"
            rows="8"
            class="w-full bg-[#0f172a] border border-gray-600 text-gray-200 rounded-lg px-4 py-3 focus:ring-2 focus:ring-alt focus:border-alt outline-none"
          ><?= $game->description ?></textarea>
        </div>

        <!-- Archivos -->
        <div class="space-y-6">
          <div>
            <label for="coverFile" class="block mb-2 text-gray-300 font-medium">Portada (600x900)</label>
            <file-upload id="coverFile" min-image-width="600" max-image-width="600" min-image-height="900" max-image-height="900"></file-upload>
          </div>

          <div>
            <label for="thumbFile" class="block mb-2 text-gray-300 font-medium">Miniatura (920x430)</label>
            <file-upload id="thumbFile" min-image-width="920" max-image-width="920" min-image-height="430" max-image-height="430"></file-upload>
          </div>

          <div>
            <label for="iconFile" class="block mb-2 text-gray-300 font-medium">Icono (32x32–512x512, cuadrado)</label>
            <file-upload id="iconFile" min-image-width="32" max-image-width="512" min-image-height="32" max-image-height="512" image-aspect-ratio="1:1"></file-upload>
          </div>
        </div>

        <!-- Botones -->
        <div class="flex flex-col gap-3 mt-6">
          <button
            id="submitButtonEdit"
            type="submit"
            class="flex justify-center items-center gap-2 bg-alt text-[#1B2A49] font-medium px-6 py-3 rounded-lg hover:opacity-90 transition w-full"
          >
            <i class="bi bi-arrow-repeat hidden animate-spin" id="spinnerEdit"></i>
            <span>Cambiar</span>
          </button>
          <button
            id="changeVisibility"
            data-status="<?= $game->is_public ? "public" : "hidden" ?>"
            class="<?= $game->is_public
                ? "bg-yellow-500 hover:bg-yellow-400"
                : "bg-green-500 hover:bg-green-400" ?> text-[#1B2A49] font-medium px-6 py-3 rounded-lg transition w-full"
          >
            <?= $game->is_public ? "Ocultar" : "Publicar" ?>
          </button>
        </div>
      </form>
    </div>

    <!-- Pestaña Builds -->
    <div id="builds" class="tab-pane hidden">
      <form id="buildForm" class="space-y-6" enctype="multipart/form-data">
        <div class="relative">
          <input
            type="text"
            id="version"
            name="version"
            placeholder=" "
            class="peer w-full bg-[#0f172a] border border-gray-600 text-gray-200 rounded-lg px-4 pt-6 pb-2 focus:ring-2 focus:ring-alt focus:border-alt outline-none placeholder-transparent"
          />
          <label
            for="version"
            class="absolute left-4 top-3 text-gray-400 text-sm transition-all
                   peer-placeholder-shown:top-4 peer-placeholder-shown:text-base peer-placeholder-shown:text-gray-500
                   peer-focus:top-2.5 peer-focus:text-sm peer-focus:text-alt"
          >
            Versión
          </label>
        </div>

        <div>
          <label for="file" class="block mb-2 text-gray-300 font-medium">Compilación</label>
          <input
            id="file"
            name="file"
            type="file"
            accept="application/zip"
            class="w-full text-gray-300 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:bg-alt file:text-[#1B2A49] hover:file:opacity-90 transition"
          />
        </div>

        <!-- Barra de progreso -->
        <div class="hidden w-full bg-gray-800 rounded-full h-2.5 overflow-hidden" id="uploadProgressContainer">
          <div id="uploadProgress" class="bg-alt h-2.5 w-0 transition-all duration-300"></div>
        </div>

        <button
          id="submitButtonBuild"
          type="submit"
          class="flex justify-center items-center gap-2 bg-alt text-[#1B2A49] font-medium px-6 py-3 rounded-lg hover:opacity-90 transition w-full"
        >
          <i class="bi bi-arrow-repeat hidden animate-spin" id="spinnerBuild"></i>
          <span>Subir</span>
        </button>
      </form>
    </div>
  </div>
</div>

            <script src="/assets/js/forms/dev/getStoreID.js"></script>
            <script src="/assets/js/forms/validator.js"></script>

            <script src="/assets/js/orion-panel/dev/store.js"></script>
            <?php
}

include "views/templates/panel/dev.php";

unset($GLOBALS["game"]);

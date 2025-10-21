<?php

$title = "Nuevo logro | Orion Dev Panel";

function showPage()
{
    global $game;
    global $stats;
    ?>

<script src="/assets/js/components/fileUpload.js"></script>

<div id="create-achievement" class="tab-pane block">
  <form id="newAchievementForm" class="space-y-6">
    <?php OrionComponents::TokenInput(ETOKEN_TYPE::DEVACTION, [
        "userID" => $_SESSION["user"]["id"],
        "gameID" => $game->id,
    ]); ?>
    <input type="hidden" name="game" value="<?= $game->id ?>">

    <!-- Nombre -->
    <div class="relative">
      <input
        type="text"
        id="name"
        name="name"
        placeholder=" "
        class="peer w-full bg-[#0f172a] border border-gray-600 text-gray-200 rounded-lg px-4 pt-6 pb-2
               focus:ring-2 focus:ring-alt focus:border-alt outline-none placeholder-transparent"
      />
      <label
        for="name"
        class="absolute left-4 top-3 text-gray-400 text-sm transition-all
               peer-placeholder-shown:top-4 peer-placeholder-shown:text-base peer-placeholder-shown:text-gray-500
               peer-focus:top-2.5 peer-focus:text-sm peer-focus:text-alt"
      >
        Nombre
      </label>
      <div class="invalid-feedback text-red-400 mt-1 text-sm" id="nameError"></div>
    </div>

    <!-- Descripción -->
    <div class="relative">
      <input
        type="text"
        id="description"
        name="description"
        placeholder=" "
        class="peer w-full bg-[#0f172a] border border-gray-600 text-gray-200 rounded-lg px-4 pt-6 pb-2
               focus:ring-2 focus:ring-alt focus:border-alt outline-none placeholder-transparent"
      />
      <label
        for="description"
        class="absolute left-4 top-3 text-gray-400 text-sm transition-all
               peer-placeholder-shown:top-4 peer-placeholder-shown:text-base peer-placeholder-shown:text-gray-500
               peer-focus:top-2.5 peer-focus:text-sm peer-focus:text-alt"
      >
        Descripción
      </label>
      <div class="invalid-feedback text-red-400 mt-1 text-sm" id="descriptionError"></div>
    </div>

    <!-- Tipo -->
    <div>
      <label for="type" class="block mb-2 text-gray-300 font-medium">Tipo</label>
      <select
        id="type"
        name="type"
        class="w-full bg-[#0f172a] border border-gray-600 text-gray-200 rounded-lg px-4 py-3
               focus:ring-2 focus:ring-alt focus:border-alt outline-none"
      >
        <option value="0">Por activación</option>
        <option value="1">Por estadística</option>
      </select>
      <div class="invalid-feedback text-red-400 mt-1 text-sm" id="typeError"></div>
    </div>

    <!-- Estadística asociada -->
    <div id="statContainer" class="hidden">
      <label for="stat" class="block mb-2 text-gray-300 font-medium">Estadística asociada</label>
      <select
        id="stat"
        name="stat"
        class="w-full bg-[#0f172a] border border-gray-600 text-gray-200 rounded-lg px-4 py-3
               focus:ring-2 focus:ring-alt focus:border-alt outline-none"
      >
        <option value="-1">Selecciona una estadística</option>
        <?php foreach ($stats as $stat): ?>
          <option value="<?= $stat->id ?>"><?= $stat->name ?></option>
        <?php endforeach; ?>
      </select>
      <div class="invalid-feedback text-red-400 mt-1 text-sm" id="statError"></div>
    </div>

    <!-- ¿Es secreto? -->
    <div>
        <div class="flex items-center gap-2">
          <input
            id="secret"
            name="secret"
            type="checkbox"
            class="w-4 h-4 bg-gray-700 border-gray-600 rounded focus:ring-2 focus:ring-alt focus:border-alt"
          >
          <label for="secret" class="text-gray-300 font-medium">¿Es secreto?</label>
        </div>
      <div class="invalid-feedback text-red-400 mt-1 text-sm" id="secretError"></div>
    </div>

    <!-- Icono -->
    <div>
      <label for="icon" class="block mb-2 text-gray-300 font-medium">Icono (64x64 PNG)</label>
      <file-upload
        id="icon"
        name="icon"
        accept-image="true"
        accept-video="false"
        min-image-width="64"
        max-image-width="64"
        min-image-height="64"
        max-image-height="64"
        max-image-size="1MB"
        image-type="png"
      ></file-upload>
      <div class="invalid-feedback text-red-400 mt-1 text-sm" id="iconError"></div>
    </div>

    <!-- Icono bloqueado -->
    <div>
      <label for="lockedIcon" class="block mb-2 text-gray-300 font-medium">Icono bloqueado (opcional, 64x64 PNG)</label>
      <file-upload
        id="lockedIcon"
        name="lockedIcon"
        accept-image="true"
        accept-video="false"
        min-image-width="64"
        max-image-width="64"
        min-image-height="64"
        max-image-height="64"
        max-image-size="1MB"
        image-type="png"
      ></file-upload>
      <div class="invalid-feedback text-red-400 mt-1 text-sm" id="lockedIconError"></div>
    </div>

    <!-- Botón -->
    <div class="flex flex-col gap-3 mt-6">
      <button
        id="submitButton"
        type="submit"
        class="flex justify-center items-center gap-2 bg-alt text-[#1B2A49] font-medium px-6 py-3 rounded-lg hover:opacity-90 transition w-full"
      >
        <i class="bi bi-arrow-repeat hidden animate-spin" id="spinnerAchievement"></i>
        <span>Crear logro</span>
      </button>
    </div>
  </form>
</div>

            <script src="/assets/js/forms/dev/getStoreID.js"></script>
            <script src="/assets/js/forms/validator.js"></script>
            <script src="/assets/js/forms/dev/achievement.js"></script>

            <?php
}

include "views/templates/panel/dev.php";

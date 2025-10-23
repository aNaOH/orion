<?php

$title = "Nuevo juego | Orion Dev Panel";

function showPage()
{
    ?>

    <div class="container mx-auto px-4">
        <form id="newGameForm" class="max-w-lg mx-auto space-y-6">
          <!-- Título -->
          <div class="relative">
            <input
              type="text"
              id="title"
              name="title"
              placeholder=" "
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
            <div id="titleError" class="hidden text-red-500 text-sm mt-1"></div>
          </div>

          <!-- Descripción corta -->
          <div class="relative">
            <input
              type="text"
              id="shortDescription"
              name="shortDescription"
              placeholder=" "
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
            <div id="shortDescriptionError" class="hidden text-red-500 text-sm mt-1"></div>
          </div>

          <!-- Checkbox -->
          <div class="flex items-center gap-2">
            <input
              id="asEditor"
              name="asEditor"
              type="checkbox"
              class="w-4 h-4 accent-alt focus:ring-alt"
            />
            <label for="asEditor" class="text-gray-300 font-medium">¿Eres la editora?</label>
          </div>

          <!-- Desarrollador -->
          <div id="developerNameContainer" class="hidden relative">
            <input
              type="text"
              id="developerName"
              name="developerName"
              placeholder=" "
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
            <div id="developerNameError" class="hidden text-red-500 text-sm mt-1"></div>
          </div>

          <!-- Botón con spinner -->
          <button
            id="submitButton"
            type="submit"
            class="flex items-center justify-center gap-2 bg-alt text-[#1B2A49] font-medium px-6 py-3 rounded-lg hover:opacity-90 transition disabled:opacity-50 disabled:cursor-not-allowed w-full"
          >
            <span>Crear</span>
            <i id="spinner" class="bi bi-arrow-repeat animate-spin hidden"></i>
          </button>
        </form>
    </div>

            <script src="/assets/js/forms/validator.js"></script>
            <script src="/assets/js/forms/dev/game.js"></script>

            <?php
}

include "views/templates/panel/dev.php";

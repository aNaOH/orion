<?php

$title = "Editar comunidad para " . $game->title . " | Orion Dev Panel";

//$hasTable = "/admin/js/tables/dev/games.js";

function showPage()
{
    global $game; ?>

    <main class="p-8">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-xl font-semibold text-alt">Editar comunidad para <?= $game->title ?></h1>
            <a href="/dev/panel/games/" class="text-gray-400 hover:text-alt transition">Volver</a>
        </div>

      <!-- Sección superior -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
        <!-- Logros -->
        <div
          class="group bg-[#1B2A49]/50 hover:bg-[#1B2A49]/70 backdrop-blur-md rounded-2xl p-6 shadow-lg border border-[#1B2A49]/60 text-center transition-all duration-300 transform hover:-translate-y-1 hover:shadow-2xl"
        >
          <i class="bi bi-trophy-fill text-alt text-4xl mb-3 group-hover:scale-110 transition-transform duration-200"></i>
          <h3 class="text-lg font-semibold mb-4 text-alt">Logros</h3>
          <p class="text-gray-300 text-sm mb-6">
            Crea, edita y elimina logros para que los jugadores puedan desbloquearlos dentro del juego.
          </p>
          <a
            href="/dev/panel/games/<?= $game->id ?>/community/achievements"
            class="inline-flex items-center justify-center px-4 py-2 rounded-lg font-medium transition text-center bg-alt text-[#1B2A49] hover:bg-yellow-400 w-full transition-transform hover:scale-[1.02]"
          >Gestionar logros</a>
        </div>

        <!-- Estadísticas -->
        <div
          class="group bg-[#1B2A49]/50 hover:bg-[#1B2A49]/70 backdrop-blur-md rounded-2xl p-6 shadow-lg border border-[#1B2A49]/60 text-center transition-all duration-300 transform hover:-translate-y-1 hover:shadow-2xl"
        >
          <i class="bi bi-bar-chart-fill text-alt text-4xl mb-3 group-hover:scale-110 transition-transform duration-200"></i>
          <h3 class="text-lg font-semibold mb-4 text-alt">Estadísticas</h3>
          <p class="text-gray-300 text-sm mb-6">
            Define métricas personalizadas para seguir el progreso y rendimiento de los jugadores.
          </p>
          <a
            href="/dev/panel/games/<?= $game->id ?>/community/stats"
            class="inline-flex items-center justify-center px-4 py-2 rounded-lg font-medium transition text-center bg-alt text-[#1B2A49] hover:bg-yellow-400 w-full transition-transform hover:scale-[1.02]"
          >Gestionar estadísticas</a>
        </div>

        <!-- Tablas de Clasificación -->
        <div
          class="group bg-[#1B2A49]/50 hover:bg-[#1B2A49]/70 backdrop-blur-md rounded-2xl p-6 shadow-lg border border-[#1B2A49]/60 text-center transition-all duration-300 transform hover:-translate-y-1 hover:shadow-2xl"
        >
          <i class="bi bi-trophy text-alt text-4xl mb-3 group-hover:scale-110 transition-transform duration-200"></i>
          <h3 class="text-lg font-semibold mb-4 text-alt">Tablas de Clasificación</h3>
          <p class="text-gray-300 text-sm mb-6">
            Crea tablas usando las estadísticas del juego para comparar puntuaciones entre jugadores.
          </p>
          <a
            href="/dev/panel/games/<?= $game->id ?>/community/leaderboards"
            class="inline-flex items-center justify-center px-4 py-2 rounded-lg font-medium transition text-center bg-alt text-[#1B2A49] hover:bg-yellow-400 w-full transition-transform hover:scale-[1.02]"
          >Gestionar tablas</a>
        </div>
      </div>

      <!-- Sección inferior -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <!-- Normas -->
        <div
          class="group bg-[#1B2A49]/50 hover:bg-[#1B2A49]/70 backdrop-blur-md rounded-2xl p-6 shadow-lg border border-[#1B2A49]/60 transition-all duration-300 transform hover:-translate-y-1 hover:shadow-2xl"
        >
          <div class="flex flex-col items-center text-center">
            <i class="bi bi-book text-alt text-4xl mb-3 group-hover:scale-110 transition-transform duration-200"></i>
            <h3 class="text-lg font-semibold mb-4 text-alt">Normas de la comunidad</h3>
            <p class="text-gray-300 text-sm mb-6">
              Administra las reglas que los usuarios deben seguir dentro de tu comunidad.
            </p>
            <div class="flex flex-col sm:flex-row gap-3 w-full">
              <a
                href="/dev/panel/games/<?= $game->id ?>/community/rules"
                class="inline-flex items-center justify-center px-4 py-2 rounded-lg font-medium transition text-center bg-alt text-[#1B2A49] hover:bg-yellow-400 flex-1 transition-transform hover:scale-[1.02]"
              >Editar normas</a>
              <a
                href="/communities/<?= $game->id ?>/rules"
                class="inline-flex items-center justify-center px-4 py-2 rounded-lg font-medium transition text-center border border-alt text-alt hover:bg-alt hover:text-[#1B2A49] flex-1 transition-transform hover:scale-[1.02]"
              >Ver normas</a>
            </div>
          </div>
        </div>

        <!-- Noticias -->
        <div
          class="group bg-[#1B2A49]/50 hover:bg-[#1B2A49]/70 backdrop-blur-md rounded-2xl p-6 shadow-lg border border-[#1B2A49]/60 transition-all duration-300 transform hover:-translate-y-1 hover:shadow-2xl"
        >
          <div class="flex flex-col items-center text-center">
            <i class="bi bi-megaphone-fill text-alt text-4xl mb-3 group-hover:scale-110 transition-transform duration-200"></i>
            <h3 class="text-lg font-semibold mb-4 text-alt">Noticias</h3>
            <p class="text-gray-300 text-sm mb-6">
              Publica noticias o actualizaciones importantes sobre tu juego.
            </p>
            <div class="flex flex-col sm:flex-row gap-3 w-full">
              <a
                href="/dev/panel/games/<?= $game->id ?>/community/news"
                class="inline-flex items-center justify-center px-4 py-2 rounded-lg font-medium transition text-center bg-alt text-[#1B2A49] hover:bg-yellow-400 flex-1 transition-transform hover:scale-[1.02]"
              >Gestionar noticias</a>
              <a
                href="/communities/<?= $game->id ?>/news"
                class="inline-flex items-center justify-center px-4 py-2 rounded-lg font-medium transition text-center border border-alt text-alt hover:bg-alt hover:text-[#1B2A49] flex-1 transition-transform hover:scale-[1.02]"
              >Ver noticias</a>
            </div>
          </div>
        </div>
      </div>
    </main>



            <?php
}

include "views/templates/panel/dev.php";

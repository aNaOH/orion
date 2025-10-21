<?php

$title = "Logros para " . $game->title . " | Orion Dev Panel";

$hasTable = "/admin/js/tables/dev/achievements.js";

function showPage()
{
    global $game;
    global $achievements;
    ?>

    <div class="flex justify-between items-center mb-6">
        <div class="flex items-center gap-2">
            <a href="/dev/panel/games/<?= $game->id ?>/community/" class="text-alt font-medium hover:opacity-90 transition">
                <i class="bi bi-arrow-left me-1"></i>
            </a>
          <h1 class="text-xl font-semibold text-alt">Logros para <?= $game->title ?></h1>
        </div>
      <a
        href="/dev/panel/games/<?= $game->id ?>/community/achievements/new"
        id="add-entry"
        class="bg-alt text-[#1B2A49] px-4 py-2 rounded-lg font-medium hover:opacity-90 transition"
      >
        <i class="bi bi-plus-lg me-1"></i> Nuevo logro
      </a>
    </div>

    <div class="overflow-x-auto">
      <table
        class="min-w-full border-separate border-spacing-0 rounded-lg overflow-hidden bg-[#111827] text-gray-200"
      >
        <thead>
          <tr class="bg-[#1B2A49] text-left">
            <th class="px-4 py-3 font-semibold">ID</th>
            <th class="px-4 py-3 font-semibold">Nombre</th>
            <th class="px-4 py-3 font-semibold">Descripción</th>
            <th class="px-4 py-3 font-semibold">Tipo</th>
            <th class="px-4 py-3 font-semibold">Estadística asociada</th>
            <th class="px-4 py-3 font-semibold">¿Secreto?</th>
            <th class="px-4 py-3 font-semibold">Icono</th>
            <th class="px-4 py-3 font-semibold">Icono (bloqueado)</th>
            <th class="px-4 py-3 font-semibold text-right">Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($achievements as $achievement) { ?>
          <tr class="border-t border-gray-700 hover:bg-[#1f2937] transition">
            <td class="px-4 py-3 text-center">
              <?= $achievement->id ?>
            </td>
            <td class="px-4 py-3">
                <?= $achievement->name ?>
            </td>
            <td class="px-4 py-3">
                <?= $achievement->description ?>
            </td>
            <td class="px-4 py-3">
                <?= $achievement->type == EACHIEVEMENT_TYPE::TRIGGERED
                    ? "Por activación"
                    : "Por estadística" ?>
            </td>
            <td class="px-4 py-3">
                <?= $achievement->type == EACHIEVEMENT_TYPE::STAT &&
                !is_null($achievement->stat_id)
                    ? $achievement->stat_id
                    : "No" ?>
            </td>
            <td class="px-4 py-3">
                <?= $achievement->secret ? "Si" : "No" ?>
            </td>
            <td class="px-4 py-3">
                <img src="/media/game/achievement/<?= $achievement->icon ?>" alt="Ícono para el logro de <?= $game->name ?> '<?= $achievement->name ?>'">
            </td>
            <td class="px-4 py-3">
              <img src="/media/game/achievement/<?= $achievement->locked_icon ?>" alt="Ícono para el logro de <?= $game->name ?> '<?= $achievement->name ?>' bloqueado">
            </td>
            <td class="px-4 py-3 text-right">
                <a
                  href="/dev/panel/games/<?= $game->id ?>/community/achievements/<?= $achievement->id ?>/edit/"
                  class="text-alt hover:opacity-80 mx-1 tooltip-btn"
                  data-tooltip="Editar"
                >
                  <i class="bi bi-pencil-square"></i>
                </a>
                <button
                  class="text-red-500 hover:opacity-80 mx-1 tooltip-btn bg-transparent border-none"
                  data-tooltip="Eliminar"
                >
                  <i class="bi bi-trash-fill"></i>
                </button>
            </td>
          </tr>
          <?php } ?>
        </tbody>
      </table>
    </div>

    <div id="table-tooltip" class="hidden absolute bg-[#1B2A49] text-[#FFD700] text-sm px-2 py-1 rounded-md shadow-lg pointer-events-none z-50"></div>


            <?php
}

include "views/templates/panel/dev.php";

<?php

$title = "Tus juegos | Orion Dev Panel";

function showPage()
{
    global $games; ?>

    <div class="flex justify-between items-center mb-6">
      <h1 class="text-xl font-semibold text-alt">Tus juegos</h1>
      <a
        href="/dev/panel/games/new"
        id="add-entry"
        class="bg-alt text-[#1B2A49] px-4 py-2 rounded-lg font-medium hover:opacity-90 transition"
      >
        <i class="bi bi-plus-lg me-1"></i> Nuevo juego
      </a>
    </div>

    <div class="overflow-x-auto">
      <table
        class="min-w-full border-separate border-spacing-0 rounded-lg overflow-hidden bg-[#111827] text-gray-200"
      >
        <thead>
          <tr class="bg-[#1B2A49] text-left">
            <th class="px-4 py-3 font-semibold">Título</th>
            <th class="px-4 py-3 font-semibold">Precio base</th>
            <th class="px-4 py-3 font-semibold">Descuento</th>
            <th class="px-4 py-3 font-semibold">¿Como editora?</th>
            <th class="px-4 py-3 font-semibold">Desarrolladora</th>
            <th class="px-4 py-3 font-semibold">Género</th>
            <th class="px-4 py-3 font-semibold">Tienda</th>
            <th class="px-4 py-3 font-semibold">Comunidad</th>
            <th class="px-4 py-3 font-semibold text-right">Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($games as $game) { ?>
          <tr class="border-t border-gray-700 hover:bg-[#1f2937] transition">
            <td class="px-4 py-3 flex gap-2 items-center">
              <img
                src="/media/game/icon/<?= $game->id ?>"
                alt="<?= $game->title ?>"
                class="w-5 h-5 rounded"
              />
              <?= $game->title ?>
            </td>
            <td class="px-4 py-3">
              <?= $game->base_price == 0
                  ? "Gratis"
                  : strval($game->base_price) . " €" ?>
            </td>
            <td class="px-4 py-3">
              <?= $game->discount == 0
                  ? "No"
                  : strval($game->discount * 100) . " %" ?>
            </td>
            <td class="px-4 py-3"><?= $game->as_editor ? "Si" : "No" ?></td>
            <td class="px-4 py-3">
              <?= $game->as_editor
                  ? $game->developer_name
                  : $game->getDeveloper()->name ?>
            </td>
            <td class="px-4 py-3">
              <?= $game->getGenre() ? $game->getGenre()->name : "N/A" ?>
            </td>
            <td class="px-4 py-3">
              <?= $game->is_public
                  ? '<a class="text-alt hover:opacity-80" href="/store/' .
                      strval($game->id) .
                      '">Ir a la tienda</a>'
                  : "No está disponible" ?>
            </td>
            <td class="px-4 py-3">
              <?= $game->is_public
                  ? '<a class="text-alt hover:opacity-80" href="/communities/' .
                      strval($game->id) .
                      '">Ir a la comunidad</a>'
                  : "No está disponible" ?>
            </td>
            <td class="px-4 py-3 text-right">
                <a
                  href="/dev/panel/games/<?= $game->id ?>/store/"
                  class="text-alt hover:opacity-80 mx-1 tooltip-btn"
                  data-tooltip="Tienda"
                >
                  <i class="bi bi-bag-fill"></i>
                </a>
              <a
                href="/dev/panel/games/<?= $game->id ?>/community/"
                class="text-alt hover:opacity-80 mx-1 tooltip-btn"
                data-tooltip="Comunidad"
              >
                <i class="bi bi-people-fill"></i>
              </a>
            </td>
          </tr>
          <?php } ?>
        </tbody>
      </table>
    </div>

    <div id="table-tooltip" class="hidden absolute bg-[#1B2A49] text-[#FFD700] text-sm px-2 py-1 rounded-md shadow-lg pointer-events-none z-50"></div>

    <script>
      const tableTooltip = document.getElementById("table-tooltip");

      document.querySelectorAll(".tooltip-btn").forEach((btn) => {
        btn.addEventListener("mouseenter", (e) => {
          const text = btn.getAttribute("data-tooltip");
          tableTooltip.textContent = text;
          tableTooltip.classList.remove("hidden");
          const rect = btn.getBoundingClientRect();
          tableTooltip.style.left = `${rect.left + rect.width / 2}px`;
          tableTooltip.style.top = `${rect.top - 30}px`;
          tableTooltip.style.transform = "translateX(-50%)";
        });

        btn.addEventListener("mouseleave", () => {
          tableTooltip.classList.add("hidden");
        });

        // Para móviles (tocar muestra el tooltip un instante)
        btn.addEventListener("touchstart", (e) => {
          const text = btn.getAttribute("data-tooltip");
          tableTooltip.textContent = text;
          tableTooltip.classList.remove("hidden");
          const rect = btn.getBoundingClientRect();
          tableTooltip.style.left = `${rect.left + rect.width / 2}px`;
          tableTooltip.style.top = `${rect.top - 30}px`;
          tableTooltip.style.transform = "translateX(-50%)";
          setTimeout(() => tableTooltip.classList.add("hidden"), 1500);
        });
      });
    </script>

    <?php
}

include "views/templates/panel/dev.php";

//<a
//  href="/dev/panel/games/1/edit/"
//  class="text-alt hover:opacity-80 mx-1 tooltip-btn"
//  data-tooltip="Editar"
//>
//  <i class="bi bi-pencil-square"></i>
//</a>
//<button
//  class="text-red-500 hover:opacity-80 mx-1 tooltip-btn bg-transparent border-none"
//  data-tooltip="Eliminar"
//>
//  <i class="bi bi-trash-fill"></i>
//</button>

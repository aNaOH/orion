<?php

$title = "Géneros | Orion Admin Panel";

function showPage()
{
    global $gamegenres; ?>
    <link rel="stylesheet" href="/assets/vendor/animate.css/animate.min.css">

    <script src="/assets/js/components/gradientSquare.js"></script>
    <script src="/assets/js/components/gradientChip.js"></script>

    <div class="flex justify-between items-center mb-6">
        <h1 class="text-xl font-semibold text-alt">Géneros</h1>
      <a
        href="/admin/gamegenres/new"
        id="add-entry"
        class="bg-alt text-[#1B2A49] px-4 py-2 rounded-lg font-medium hover:opacity-90 transition"
      >
        <i class="bi bi-plus-lg me-1"></i> Nuevo género
      </a>
    </div>

    <div class="overflow-x-auto">
      <table
        class="min-w-full border-separate border-spacing-0 rounded-lg overflow-hidden bg-[#111827] text-gray-200"
      >
        <thead>
          <tr class="bg-[#1B2A49] text-left">
            <th class="px-4 py-3 font-semibold">Nombre</th>
            <th class="px-4 py-3 font-semibold">Color distintivo (en hexadecimal)</th>
            <th class="px-4 py-3 font-semibold">Previsualización</th>
            <th class="px-4 py-3 font-semibold text-right">Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($gamegenres as $genre) { ?>
          <tr class="border-t border-gray-700 hover:bg-[#1f2937] transition">
            <td class="px-4 py-3">
              <?= $genre->name ?>
            </td>
            <td class="px-4 py-3">
                <?= $genre->tint ?>
            </td>
            <td class="px-4 py-3">
                <gradient-chip base-color="<?= $genre->tint ?>" size="25" text="<?= $genre->name ?>"></gradient-chip>
            </td>
            <td class="px-4 py-3 text-right">
                <a
                  href="/dev/panel/gamegenres/<?= $genre->id ?>/edit/"
                  class="text-alt hover:opacity-80 mx-1 tooltip-btn"
                  data-tooltip="Editar"
                >
                  <i class="bi bi-pencil-square"></i>
                </a>
                <button
                  class="text-red-500 hover:opacity-80 mx-1 tooltip-btn bg-transparent border-none delete-btn"
                  data-tooltip="Eliminar"
                  data-id="<?= $genre->id ?>"
                  data-name="<?= htmlspecialchars($genre->name) ?>"
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

    <script src="/assets/js/orion-panel/table-tooltip.js"></script>

    <script src="/assets/js/orion-panel/delete-popup.js"></script>
    <script>
      // invoca después de que el DOM esté listo (al final del body o DOMContentLoaded)
      setupDeletePopup({
        selector: ".delete-btn",
        getName: (btn) => btn.dataset.name,
        getDeleteUrl: (btn) => `/api/admin/gamegenres/${btn.dataset.id}/delete/`,
        title: "¿Eliminar género?",
        onConfirm: (url) => {
          fetch(url, { method: 'DELETE' })
            .then(response => {
              if (response.ok) {
                window.location.href = '/admin/gamegenres/';
              } else {
                console.error('Error al eliminar el género');
              }
            })
            .catch(error => console.error('Error al eliminar el género:', error));
        }
      });
    </script>
            <?php
}

include "views/templates/panel/admin.php";

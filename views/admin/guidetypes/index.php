<?php

$title = "Tipos de guía | Orion Dev Panel";

function showPage()
{
    global $guidetypes; ?>

    <script src="/assets/js/components/gradientSquare.js"></script>
    <script src="/assets/js/components/gradientChip.js"></script>

    <div class="flex justify-between items-center mb-6">
        <h1 class="text-xl font-semibold text-alt">Tipos de guía</h1>
      <a
        href="/admin/guidetypes/new"
        id="add-entry"
        class="bg-alt text-[#1B2A49] px-4 py-2 rounded-lg font-medium hover:opacity-90 transition"
      >
        <i class="bi bi-plus-lg me-1"></i> Nuevo tipo de guía
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
            <th class="px-4 py-3 font-semibold">Previsualización (Cuadrado)</th>
            <th class="px-4 py-3 font-semibold">Previsualización (Chip)</th>
            <th class="px-4 py-3 font-semibold text-right">Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($guidetypes as $guidetype) { ?>
          <tr class="border-t border-gray-700 hover:bg-[#1f2937] transition">
            <td class="px-4 py-3">
              <?= $guidetype->type ?>
            </td>
            <td class="px-4 py-3">
                <?= $guidetype->tint ?>
            </td>
            <td class="px-4 py-3">
                <gradient-square icon-path="/media/guidetype/<?= $guidetype->icon ?>" base-color="<?= $guidetype->tint ?>" size="50"></gradient-square>
            </td>
            <td class="px-4 py-3">
                <gradient-chip icon-path="/media/guidetype/<?= $guidetype->icon ?>" base-color="<?= $guidetype->tint ?>" size="25" text="<?= $guidetype->type ?>"></gradient-chip>
            </td>
            <td class="px-4 py-3 text-right">
                <a
                  href="/dev/panel/guidetypes/<?= $guidetype->id ?>/edit/"
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

include "views/templates/panel/admin.php";

<?php

$title = "Inicio | Orion Admin Panel";

function showPage()
{
    ?>
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
              <div class="bg-brand-800 rounded-2xl p-5 shadow-lg border border-branddark-500 hover:border-alt-500 transition">
                <h4 class="text-sm text-gray-300 mb-2">Ingresos (últimos 30 días)</h4>
                <p class="text-4xl font-bold text-alt-400">0 €</p>
              </div>
              <div class="bg-brand-800 rounded-2xl p-5 shadow-lg border border-branddark-500 hover:border-alt-500 transition">
                <h4 class="text-sm text-gray-300 mb-2">Reportes</h4>
                <p class="text-4xl font-bold text-alt-400">0</p>
              </div>
            </div>
    <?php
}

include "views/templates/panel/admin.php";

<?php

$title = "Tu perfil en Orion";

function showPage()
{
    global $user;
    global $is_self;
    ?>

    <!-- Profile Page -->
    <section id="profile" class="py-20">
      <div class="container mx-auto max-w-4xl bg-branddark shadow-lg rounded-lg p-8">
        <!-- Header -->
        <div class="flex items-center space-x-6">
          <!-- Profile Picture -->
          <div class="w-32 h-32 rounded-full overflow-hidden border-4 border-alt-500">
            <img src="https://cdn.orion.moonnastd.com/profile/<?= $user->profile_pic ??
                "default" ?>" alt="Foto de perfil de <?= $user->username ?>" class="w-full h-full object-cover">
          </div>
          <!-- User Info -->
          <div>
            <h1 class="text-4xl font-bold text-gray-200"><?= $user->username ?> <span class="text-2xl font-normal text-gray-400"><?= $user->getHandle() ?></span></h1>
            <p class="text-gray-400 text-lg"><?= $user->motd ??
                "Sin mensaje personalizado." ?></p>
            <?php if (isset($is_self) && $is_self) { ?>
              <div class="mt-6">
                <a href="/profile/edit" class="px-4 py-2 bg-alt text-gray-200 font-semibold rounded-lg shadow-lg hover:bg-alt-400 transition">
                  Editar perfil
                </a>
              </div>
            <?php } ?>
          </div>
        </div>

        <!-- Profile Details -->
        <div class="mt-10">
          <h2 class="text-2xl font-semibold text-gray-200 mb-4">Acerca del Usuario</h2>
          <div class="bg-brand-900 rounded-lg p-6 shadow-sm">
              <h3 class="text-lg font-semibold text-gray-200">Fecha de Registro</h3>
              <p class="text-gray-400">Miembro desde <?= $user->created_at ?></p>
          </div>
        </div>
      </div>
    </section>


    <?php
}

include "views/templates/main.php";

unset($GLOBALS["user"]);
if (isset($GLOBALS["is_self"])) {
    unset($GLOBALS["is_self"]);
}

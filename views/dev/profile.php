<?php

$title = "Tu perfil en Orion";

function showPage() {

  global $developer;
    ?>

    <!-- Profile Page -->
    <section id="profile" class="py-20">
      <div class="container mx-auto max-w-4xl bg-branddark shadow-lg rounded-lg p-8">
        <!-- Header -->
        <div class="flex items-center space-x-6">
          <!-- Profile Picture -->
          <div class="w-32 h-32 rounded-full overflow-hidden border-4 border-alt-500">
            <img src="/media/profile/<?= $developer->profile_pic ?? 'default' ?>" alt="Foto de perfil de <?= $developer->name ?>" class="w-full h-full object-cover">
          </div>
          <!-- User Info -->
          <div>
            <h1 class="text-4xl font-bold text-gray-200"><?= $developer->name ?></h1>
            <p class="text-gray-400 text-lg"><?= $developer->motd ?? 'Sin mensaje personalizado.' ?></p>
          </div>
        </div>
        
        <!-- Profile Details -->
        <div class="mt-10">
          <h2 class="text-2xl font-semibold text-gray-200 mb-4">Juegos de esta desarrolladora</h2>
          <div class="bg-brand-900 rounded-lg p-6 shadow-sm">
              
          </div>
        </div>
      </div>
    </section>


    <?php
}

include("views/templates/main.php");

unset($GLOBALS['developer']);
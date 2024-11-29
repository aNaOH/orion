<?php

$title = "Editar perfil de Orion";

function showPage() {

  global $user;
    ?>

    <!-- Profile Page -->
    <section id="profile" class="py-20">
      <form id="profileEditForm" novalidate>
        <?php OrionComponents::TokenInput(ETOKEN_TYPE::USERACTION) ?>
        <div class="container mx-auto max-w-4xl bg-branddark shadow-lg rounded-lg p-8">
          <!-- Header -->
          <div class="flex items-center space-x-6">
            <!-- Profile Picture -->
            <div class="group w-32 h-32 rounded-full overflow-hidden border-4 border-alt-500 relative cursor-pointer">
              <div 
                id="editPic" 
                class="absolute inset-0 flex items-center justify-center bg-branddark/40 opacity-0 transition-opacity group-hover:opacity-100">
                <i class="bi bi-pencil-fill text-gray-200"></i>
              </div>
              <img
                id="profilePicImg" 
                src="/media/profile/<?= $user->profile_pic ?? 'default' ?>" 
                alt="Foto de perfil de <?= $user->username ?>" 
                class="w-full h-full object-cover">
                <input 
                  type="file" 
                  id="profilePic" 
                  name="profilePic" 
                  class="hidden" 
                  accept="image/*">
            </div>
            <!-- User Info -->
            <div>
              <div class="flex flex-row items-center gap-2">
                <i class="bi bi-pencil-fill text-gray-200"></i>
                <input type="text" id="username" name="username" class="bg-transparent text-4xl font-bold text-gray-200" value="<?= $user->username ?>"/>
              </div>
              <div class="flex flex-row items-center gap-2">
                <i class="bi bi-pencil-fill text-gray-400"></i>
                <input type="text" id="motd" name="motd" class="bg-transparent text-gray-400 text-lg" value="<?= $user->motd ?? '' ?>" />
              </div>
            </div>
        </div>
        
        <!-- Profile Details -->
        <div class="mt-10">
          <h2 class="text-2xl font-semibold text-gray-200 mb-4">Ajustes de la cuenta</h2>
          <div class="bg-brand-900 rounded-lg p-6 shadow-sm">
                <h3 class="text-lg font-semibold text-gray-200">Correo electrónico</h3>
                <div class="bg-branddark-600 p-2 rounded-xl text-gray-200 w-full flex flex-row gap-2">
                  <i class="bi bi-pencil-fill"></i>
                  <input type="email" name="email" id="email" class="bg-transparent flex-1" value="<?= $user->email ?>"/>
                </div>
                <h3 class="text-lg font-semibold text-gray-200">Contraseña</h3>
                <div class="bg-branddark-600 p-2 rounded-xl text-gray-200 w-full flex flex-row gap-2">
                  <input type="password" name="currentPassword" id="currentPassword" placeholder="Contraseña actual" class="bg-transparent flex-1"/>
                </div>
                <h3 class="text-md text-gray-200">Nueva contraseña</h3>
                <div class="w-full flex flex-row gap-2">
                  <input type="password" name="password" id="password" placeholder="Nueva contraseña" class="bg-branddark-600 p-2 rounded-xl text-gray-200 flex-1"/>
                  <input type="password" name="confirmPassword" id="confirmPassword" placeholder="Confirmar contraseña" class="bg-branddark-600 p-2 rounded-xl text-gray-200 flex-1"/>
                </div>
          </div>
        </div>
        <div class="flex flex-row gap-2 mt-6">
          <input id="submitButton" type="submit" value="Guardar" class="px-4 py-2 bg-alt text-gray-200 font-semibold rounded-lg shadow-lg hover:bg-alt-400 transition cursor-pointer" />
          <a id="returnBtn" href="/profile" class="px-4 py-2 bg-alt text-gray-200 font-semibold rounded-lg shadow-lg hover:bg-alt-400 transition">
            Volver
          </a>
        </div>
      </div>
    </section>

    </form>

    <script src="/assets/js/forms/editProfile.js"></script>

    <?php
}

include("views/templates/main.php");

unset($GLOBALS['user']);
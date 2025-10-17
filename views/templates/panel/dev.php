<?php

if (isset($_SESSION["user"])) {
    $userSession = $_SESSION["user"];
    $user = User::getById($userSession["id"]);
    $developer = $user->getDeveloperInfo();
}

include "views/templates/panel/components/header.php";
include "views/templates/panel/components/dev-sidebar.php";
?>

<!-- CONTENIDO -->
<div id="main-content" class="flex-1 flex flex-col bg-branddark-800 overflow-hidden">
  <!-- HEADER -->
  <header
    class="flex items-center justify-between px-6 py-4 border-b border-branddark-600 bg-branddark-700 shadow-sm flex-shrink-0">
    <div class="flex items-center gap-3">
      <button id="sidebarToggle" class="text-gray-400 hover:text-alt-400 transition md:block hidden">
        <i class="bi bi-list text-2xl"></i>
      </button>
      <h2 class="text-xl font-lg">Panel de Desarrollador</h2>
    </div>
    <div class="flex items-center gap-5">
      <button class="hover:text-alt-400 transition">
        <i class="bi bi-bell text-xl"></i>
      </button>
    </div>
  </header>

  <!-- MAIN -->
  <main class="flex-1 p-8 overflow-y-auto">

<?php
showPage($developer);

include "views/templates/panel/components/footer.php";


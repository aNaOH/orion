<!-- SIDEBAR -->
<aside id="sidebar"
  class="bg-branddark-700 w-64 flex-shrink-0 flex flex-col border-r border-branddark-500 transition-all duration-300 z-40 overflow-hidden">
  <!-- Logo -->
  <div class="flex items-center justify-between px-6 py-5 border-b border-branddark-500">
    <img src="/assets/img/orion-logo.svg" alt="Orion Logo" id="logo" class="h-8">
    <button id="sidebarToggleMobile" class="text-gray-400 hover:text-white md:hidden">
      <i class="bi bi-x-lg text-xl"></i>
    </button>
  </div>

  <!-- Menú -->
  <nav class="flex-1 p-4 space-y-1 overflow-y-auto overflow-x-hidden">
    <a href="/admin" class="menu-item flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-branddark-500 transition-all"
      data-tooltip="Inicio">
      <i class="bi bi-speedometer2 text-alt-400 text-lg"></i>
      <span class="menu-text text-sm font-medium">Inicio</span>
    </a>
    <hr class="my-2 border-branddark-500">
    <a href="/admin/guidetypes" class="menu-item flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-branddark-500 transition-all"
      data-tooltip="Tipos de guía">
      <i class="bi bi-book-fill text-alt-400 text-lg"></i>
      <span class="menu-text text-sm font-medium">Tipos de guía</span>
    </a>
    <a href="/admin/newscategories" class="menu-item flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-branddark-500 transition-all"
      data-tooltip="Categorías de noticias">
      <i class="bi bi-newspaper text-alt-400 text-lg"></i>
      <span class="menu-text text-sm font-medium">Categorías de noticias</span>
    </a>
    <!--<a href="/admin/reports" class="menu-item flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-branddark-500 transition-all"
      data-tooltip="Reportes">
      <i class="bi bi-file-text-fill text-alt-400 text-lg"></i>
      <span class="menu-text text-sm font-medium">Reportes</span>
    </a> -->
    <hr class="my-2 border-branddark-500">
    <a href="/admin/gamegenres" class="menu-item flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-branddark-500 transition-all"
      data-tooltip="Géneros">
      <i class="bi bi-joystick text-alt-400 text-lg"></i>
      <span class="menu-text text-sm font-medium">Géneros</span>
    </a>
    <a href="/admin/gamefeatures" class="menu-item flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-branddark-500 transition-all"
      data-tooltip="Características de juegos">
      <i class="bi bi-controller text-alt-400 text-lg"></i>
      <span class="menu-text text-sm font-medium">Características</span>
    </a>
    <hr class="my-2 border-branddark-500">
    <a href="/admin/tools" class="menu-item flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-branddark-500 transition-all"
      data-tooltip="Herramientas">
      <i class="bi bi-tools text-alt-400 text-lg"></i>
      <span class="menu-text text-sm font-medium">Herramientas</span>
    </a>
  </nav>

  <!-- Volver -->
  <div class="p-4 border-t border-branddark-500">
    <a href="/" class="menu-item flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-branddark-500 transition-all w-full"
      data-tooltip="Volver a Orion">
      <i class="bi bi-arrow-left text-alt-400 text-lg"></i>
      <span class="menu-text text-sm font-medium">Volver a Orion</span>
    </a>
  </div>
</aside>

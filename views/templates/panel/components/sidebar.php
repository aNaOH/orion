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
    <a href="#dashboard" class="menu-item flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-branddark-500 transition-all"
      data-tooltip="Dashboard">
      <i class="bi bi-speedometer2 text-alt-400 text-lg"></i>
      <span class="menu-text text-sm font-medium">Dashboard</span>
    </a>
    <a href="#users" class="menu-item flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-branddark-500 transition-all"
      data-tooltip="Usuarios">
      <i class="bi bi-people text-alt-400 text-lg"></i>
      <span class="menu-text text-sm font-medium">Usuarios</span>
    </a>
    <a href="#games" class="menu-item flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-branddark-500 transition-all"
      data-tooltip="Juegos">
      <i class="bi bi-controller text-alt-400 text-lg"></i>
      <span class="menu-text text-sm font-medium">Juegos</span>
    </a>
    <a href="#content" class="menu-item flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-branddark-500 transition-all"
      data-tooltip="Contenido">
      <i class="bi bi-chat-dots text-alt-400 text-lg"></i>
      <span class="menu-text text-sm font-medium">Contenido</span>
    </a>
    <a href="#settings" class="menu-item flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-branddark-500 transition-all"
      data-tooltip="Configuración">
      <i class="bi bi-gear text-alt-400 text-lg"></i>
      <span class="menu-text text-sm font-medium">Configuración</span>
    </a>
  </nav>

  <!-- Perfil -->
  <div class="border-t border-branddark-500 p-4 flex items-center justify-between">
    <div class="flex items-center gap-3">
      <img src="/assets/img/avatar.png" alt="Admin" class="w-10 h-10 rounded-full border border-branddark-400">
      <div class="menu-text">
        <p class="text-sm font-semibold">Admin</p>
        <p class="text-xs text-gray-400">Administrador</p>
      </div>
    </div>
    <button class="text-gray-400 hover:text-alt-400 transition">
      <i class="bi bi-box-arrow-right text-lg"></i>
    </button>
  </div>
</aside>

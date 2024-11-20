<?php

$title = "Bienvenido a Orion";

function showPage() {
    ?>

    <div class="min-h-screen flex flex-col items-center justify-center">
        <!-- Hero Section -->
        <section class="text-center py-20">
            <h1 class="text-4xl md:text-6xl font-bold mb-4">
            Orion
            </h1>
            <h1 class="text-2xl md:text-4xl font-bold mb-4">
            ¡Gamers, unidos!
            </h1>
            <p class="text-lg md:text-xl mb-8">
            Descubre, comparte y gestiona videojuegos en una plataforma creada para jugadores y desarrolladores.
            </p>
            <a href="#features" class="px-6 py-3 bg-alt text-brand font-semibold rounded-lg shadow-md hover:bg-alt-light transition">
            Explorar más
            </a>
        </section>

        <!-- Features Section -->
        <section id="features" class="max-w-5xl mx-auto py-20 grid grid-cols-1 md:grid-cols-3 gap-8 text-center">
            <div class="p-6 bg-alt text-brand rounded-lg shadow-lg">
            <h2 class="text-2xl font-semibold mb-4">Comunidad Activa</h2>
            <p>Participa en foros, comparte contenido y compite en tablas de clasificaciones.</p>
            </div>
            <div class="p-6 bg-alt text-brand rounded-lg shadow-lg">
            <h2 class="text-2xl font-semibold mb-4">Herramientas para Creadores</h2>
            <p>Gestión de juegos, moderación eficiente y páginas de tienda personalizadas.</p>
            </div>
            <div class="p-6 bg-alt text-brand rounded-lg shadow-lg">
            <h2 class="text-2xl font-semibold mb-4">Logros y Más</h2>
            <p>Obtén logros únicos y comparte tus momentos destacados con la comunidad.</p>
            </div>
        </section>

        <!-- Call to Action -->
        <section class="text-center py-20 w-full">
            <h2 class="text-3xl md:text-4xl font-bold mb-4">Únete a Orion Hoy</h2>
            <p class="mb-8">Sé parte de la revolución en la interacción entre jugadores y desarrolladores.</p>
            <a href="/register" class="px-8 py-4 bg-alt text-brand font-semibold rounded-lg shadow-lg hover:bg-alt-light transition">
            Registrarme Ahora
            </a>
        </section>
    </div>

    <?php
}

include("views/templates/main.php");
<?php

$title = "Bienvenido a Orion";

function showPage() {
    ?>

    <!-- Hero Section -->
    <section id="hero" class="bg-brand-500 text-white py-20">
    <div class="container mx-auto text-center">
        <img src="/assets/img/orion-logo.svg" alt="Orion logo" class="mx-auto mb-5 w-1/4">
        <p class="text-lg md:text-xl mb-6">Descubre, comparte y gestiona videojuegos en una plataforma creada para jugadores y desarrolladores.</p>
        <a href="/explore" class="px-8 py-4 bg-alt text-white font-semibold rounded-lg shadow-lg hover:bg-alt-400 focus:ring focus:ring-alt-300 transition">
        Explorar ahora
        </a>
    </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-20">
    <div class="container mx-auto text-center">
        <h2 class="text-3xl md:text-4xl font-bold text-gray-200 mb-12">Lo que ofrecemos</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <!-- Comunidad Activa -->
        <div class="bg-branddark shadow-lg rounded-lg p-6">
            <img src="https://placehold.co/300x200" alt="Comunidad Activa" class="rounded-lg mb-4 mx-auto">
            <h3 class="text-xl font-semibold text-gray-200 mb-2">Comunidad Activa</h3>
            <p class="text-gray-400">Participa en foros, comparte contenido y compite en tablas de clasificaciones.</p>
        </div>
        <!-- Herramientas para Creadores -->
        <div class="bg-branddark shadow-lg rounded-lg p-6">
            <img src="https://placehold.co/300x200" alt="Herramientas para Creadores" class="rounded-lg mb-4 mx-auto">
            <h3 class="text-xl font-semibold text-gray-200 mb-2">Herramientas para Creadores</h3>
            <p class="text-gray-400">Gestión de juegos, moderación eficiente y páginas de tienda personalizadas.</p>
        </div>
        <!-- Logros y Más -->
        <div class="bg-branddark shadow-lg rounded-lg p-6">
            <img src="https://placehold.co/300x200" alt="Logros y Más" class="rounded-lg mb-4 mx-auto">
            <h3 class="text-xl font-semibold text-gray-200 mb-2">Logros y Más</h3>
            <p class="text-gray-400">Obtén logros únicos y comparte tus momentos destacados con la comunidad.</p>
        </div>
        </div>
    </div>
    </section>

    <!-- Call to Action Section -->
    <section class="text-white py-20">
    <div class="container mx-auto text-center">
        <h2 class="text-4xl font-bold text-alt mb-4">Únete a Orion Hoy</h2>
        <p class="text-lg mb-6">Sé parte de una comunidad única donde jugadores y desarrolladores conectan y colaboran.</p>
        <a href="/register" class="px-8 py-4 bg-alt-500 text-white font-semibold rounded-lg shadow-lg hover:bg-alt-400 focus:ring focus:ring-brand-300 transition">
        Registrarme Ahora
        </a>
    </div>
    </section>

    <?php
}

include("views/templates/main.php");
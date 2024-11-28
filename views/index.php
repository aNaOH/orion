<?php

$title = "Bienvenido a Orion";

function showPage() {
    ?>

    <!-- Hero Section -->
    <section id="hero" class="relative bg-brand-500 text-white min-h-screen flex items-center justify-center overflow-hidden">
        <div  class="absolute inset-0">
            <div class="h-full w-full bg-cover bg-center" 
                style="background-image: url('/assets/img/hero-bg-transparent.png'); mask-image: linear-gradient(to bottom, rgba(0,0,0,0.3) 0.25%, rgba(0,0,0,0)); -webkit-mask-image: linear-gradient(to bottom, rgba(0,0,0,0.3) 0.25%, rgba(0,0,0,0));">
            </div>
        </div>

        <!-- Contenido principal -->
        <div data-aos="fade-up" class="relative z-10 container mx-auto text-center">
            <img src="/assets/img/orion-logo.svg" alt="Orion logo" class="mx-auto mb-5 w-1/4 animate-fade-in">
            <p class="text-lg md:text-xl mb-6">Descubre, comparte y gestiona videojuegos en una plataforma creada para jugadores y desarrolladores.</p>
        </div>
    </section>


    <!-- Features Section -->
    <section id="features" class="py-10">
    <div>
        <h2 class="text-3xl md:text-4xl font-bold text-gray-200 mb-12 text-center">Lo que ofrecemos</h2>
        <div class="overflow-hidden flex flex-col m-5 gap-5">
            <!-- Comunidad Activa -->
            <div data-aos="fade-left" 
                class="bg-gradient-to-l from-branddark to-transparent rounded-xl p-6 text-right flex flex-row-reverse gap-4">
                <img src="https://placehold.co/300x200" alt="Comunidad Activa" class="rounded-lg mb-4">
                <div>
                    <h3 class="text-xl font-semibold text-gray-200 mb-2">Comunidad Activa</h3>
                    <p class="text-gray-400">Participa en foros, comparte contenido y compite en tablas de clasificaciones.</p>
                </div>
            </div>
            <!-- Herramientas para Creadores -->
            <div data-aos="fade-right" 
                class="bg-gradient-to-r from-branddark to-transparent rounded-xl p-6 text-left flex flex-row gap-4">
                <img src="https://placehold.co/300x200" alt="Herramientas para Creadores" class="rounded-lg mb-4">
                <div>
                    <h3 class="text-xl font-semibold text-gray-200 mb-2">Herramientas para Creadores</h3>
                    <p class="text-gray-400">Gestión de juegos, moderación eficiente y páginas de tienda personalizadas.</p>
                </div>
            </div>
            <!-- Logros y Más -->
            <div data-aos="fade-left" 
                class="bg-gradient-to-l from-branddark to-transparent rounded-xl p-6 text-right flex flex-row-reverse gap-4">
                <img src="https://placehold.co/300x200" alt="Logros y Más" class="rounded-lg mb-4">
                <div>
                    <h3 class="text-xl font-semibold text-gray-200 mb-2">Logros y Más</h3>
                    <p class="text-gray-400">Obtén logros únicos y comparte tus momentos destacados con la comunidad.</p>
                </div>
            </div>
        </div>
    </div>
</section>


    <!-- Call to Action Section -->
    <section data-aos="fade-up" class="text-white py-20">
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

include("views/templates/nomain.php");
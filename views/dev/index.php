<?php

require_once 'models/User.php';

$title = "Orion para Desarrolladores";

function showPage() {
    if(isset($_SESSION['user'])) $user = User::getById($_SESSION['user']['id']);
    ?>

    <!-- Hero Section -->
    <section id="hero" class="relative bg-brand-500 text-white min-h-screen flex items-center justify-center overflow-hidden">
        <!-- Fondo con imagen y degradado -->
        <div class="absolute inset-0">
            <div class="h-full w-full bg-cover bg-center" 
                style="background-image: url('/assets/img/hero-bg-transparent.png'); mask-image: linear-gradient(to bottom, rgba(0,0,0,0.05) 0.25%, rgba(0,0,0,0)); -webkit-mask-image: linear-gradient(to bottom, rgba(0,0,0,0.05) 0.25%, rgba(0,0,0,0));">
            </div>
        </div>

        <!-- Contenido principal -->
        <div data-aos="fade-up" class="relative z-10 container mx-auto text-center">
            <img src="/assets/img/orion-dev-logo.svg" alt="Orion for Developers logo" class="mx-auto mb-5 w-1/4 animate-fade-in">
            <p class="text-lg md:text-xl mb-6">Distribuye tus juegos en Orion, una plataforma creada para jugadores y desarrolladores.</p>
        </div>

        <div class="absolute bottom-10 left-1/2 transform -translate-x-1/2 mb-4 animate__animated animate__fadeInUp">
            <a href="#why" id="moveToWhy" class="text-white text-4xl">
                <i class="bi bi-chevron-down"></i>
            </a>
        </div>
    </section>

    <!-- Features Section -->
    <section id="why" class="py-10">
        <div class="container mx-auto px-4">
            <h2 class="text-3xl sm:text-4xl font-bold text-gray-200 mb-12 text-center">Por qué es la opción de distribución para <span id="userCount">N</span> desarrolladores y editoras</h2>
            <div class="flex flex-col gap-10">
                <!-- Comunidad Activa -->
                <div data-aos="fade-left" 
                    class="bg-gradient-to-l from-branddark to-transparent rounded-xl p-6 flex flex-col-reverse lg:flex-row-reverse items-center gap-6">
                    <img src="https://placehold.co/300x200" alt="Comunidad Activa" class="rounded-lg w-full max-w-md lg:max-w-xs">
                    <div class="text-center lg:text-right">
                        <h3 class="text-xl font-semibold text-gray-200 mb-2">Comunidad Activa</h3>
                        <p class="text-gray-400">Participa en foros, comparte contenido y compite en tablas de clasificaciones.</p>
                    </div>
                </div>
                <!-- Herramientas para Creadores -->
                <div data-aos="fade-right" 
                    class="bg-gradient-to-r from-branddark to-transparent rounded-xl p-6 flex flex-col lg:flex-row items-center gap-6">
                    <img src="https://placehold.co/300x200" alt="Herramientas para Creadores" class="rounded-lg w-full max-w-md lg:max-w-xs">
                    <div class="text-center lg:text-left">
                        <h3 class="text-xl font-semibold text-gray-200 mb-2">Herramientas para Creadores</h3>
                        <p class="text-gray-400">Gestión de juegos, moderación eficiente y páginas de tienda personalizadas.</p>
                    </div>
                </div>
                <!-- Logros y Más -->
                <div data-aos="fade-left" 
                    class="bg-gradient-to-l from-branddark to-transparent rounded-xl p-6 flex flex-col-reverse lg:flex-row-reverse items-center gap-6">
                    <img src="https://placehold.co/300x200" alt="Logros y Más" class="rounded-lg w-full max-w-md lg:max-w-xs">
                    <div class="text-center lg:text-right">
                        <h3 class="text-xl font-semibold text-gray-200 mb-2">Logros y Más</h3>
                        <p class="text-gray-400">Obtén logros únicos y comparte tus momentos destacados con la comunidad.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Call to Action Section -->
    <section data-aos="fade-up" class="text-white py-20">
    <?php if(!isset($user)) { ?>
        <div class="container mx-auto text-center">
            <h2 class="text-4xl font-bold text-alt mb-4">Únete a Orion hoy</h2>
            <p class="text-lg mb-6">Necesitas una cuenta de Orion para poder crear tu cuenta de desarrollador.</p>
            <a href="/register" class="px-8 py-4 bg-alt-500 text-white font-semibold rounded-lg shadow-lg hover:bg-alt-400 focus:ring focus:ring-brand-300 transition">
                Registrarme Ahora
            </a>
        </div>
    <?php } else { 
        if(is_null($user->getDeveloperInfo())) { ?>
        
        <div class="container mx-auto text-center">
            <h2 class="text-4xl font-bold text-alt mb-4">Publica tus juegos en Orion</h2>
            <form action="/stripe/dev" method="get">
                <div class="flex items-center space-x-2 p-2 rounded-full bg-branddark bg-opacity-75 shadow-md w-[50%] mx-auto">
                    <input type="text" class="w-full bg-transparent text-text-gray-200 placeholder-text-gray-200/75 px-4 py-2 rounded-full border-none focus:outline-none" name="devName" placeholder="Nombre de tu desarrolladora/editora">
                </div>
                <input type="submit" value="Adquirir cuenta (24,99 €)" class="mt-5 px-8 py-4 bg-alt-500 text-white font-semibold rounded-lg shadow-lg hover:bg-alt-400 focus:ring focus:ring-brand-300 transition"></input>
            </form>
        </div>

        <?php } else { ?>
            <div class="container mx-auto text-center">
                <h2 class="text-4xl font-bold text-alt mb-4">¡Ya eres desarrollador en Orion!</h2>
                <p class="text-lg mb-6">Entra a tu panel para continuar.</p>
                <a href="/dev/panel" class="px-8 py-4 bg-alt-500 text-white font-semibold rounded-lg shadow-lg hover:bg-alt-400 focus:ring focus:ring-brand-300 transition">
                    Ir al panel
                </a>
            </div>
        <?php } ?>
    <?php } ?>
    </section>

    <script src="/assets/js/dev.js"></script>

    <?php
}

include("views/templates/nomain.php");
<?php

$title = "¡Bienvenido!";

function showPage() {
    ?>

    <!-- 404 Page -->
    <section class="min-h-screen flex flex-col md:flex-row items-center justify-center text-center md:text-left p-8">
    <!-- Content -->
    <div data-aos="fade-right" class="md:w-1/2">
        <!-- Error Title -->
        <h1 class="text-6xl font-bold text-gray-200 mb-4">¡Enhorabuena!</h1>
        <h2 class="text-2xl font-semibold text-gray-400 mb-4">Pago hecho</h2>
        <p class="text-lg text-gray-400 mb-8">
        Ahora eres un desarrollador de Orion
        </p>
        <!-- Buttons -->
        <div class="flex justify-center md:justify-start space-x-4">
        <a href="/dev/panel" class="px-6 py-3 bg-alt text-white font-semibold rounded-lg shadow-lg hover:bg-alt-400 focus:ring focus:ring-brand-300 transition">
            ¡Mira tu nuevo hogar!
        </a>
        </div>
    </div>
    </section>

    <?php
}

include("views/templates/nomain.php");
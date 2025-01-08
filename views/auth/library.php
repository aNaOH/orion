<?php

$title = "Tu biblioteca en Orion";

function showPage() {
    ?>

    <script src="/assets/js/library.js"></script>

    <div id="library-loading" class="fixed top-0 left-0 w-full h-full bg-branddark z-50">
        <div class="flex flex-col items-center justify-center h-full">
            <div class="loader ease-linear rounded-full border-8 border-t-8 border-gray-200 h-32 w-32 mb-4"></div>
            <h2 class="text-center text-xl">Cargando tu biblioteca...</h2>
        </div>
    </div>
    <!-- App section -->
    <section id="app" class="p-10">
        <h1 class="text-4xl mb-5">Tu biblioteca</h1>
        <div class="container h-full w-full">
            <div class="flex flex-row gap-10 mx-auto h-full w-full">
                <div id="sidebar" class="bg-branddark flex flex-col gap-2 p-5 flex-[0.35] rounded-xl shadow-lg">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="toggleDeveloperGames" checked>
                        <label class="form-check-label" for="toggleDeveloperGames">
                            Mostrar juegos publicados por mí
                        </label>
                    </div>
                    <div id="game-list" class="flex flex-col gap-2">

                    </div>
                </div>
                <div class="flex-1 h-full">
                    <div id="game-info" class="hidden">
                        <div class="flex flex-col gap-5">
                            <div id="game-cover" class="w-1/3">
                                <img id="game-image" src="" alt="Cover" class="aspect-[2.14/1] w-full rounded-md shadow-lg">
                            </div>
                            <h1 id="game-title" class="text-3xl">Title</h1>
                            <div id="game-actions" class="flex flex-row gap-2 mb-5 items-center py-2">
                                <div id="game-download" class="flex flex-row gap-2">
                                    <span id="download" class="text-lg bg-alt hover:bg-alt-600 rounded-md transition-all duration-[50ms] p-2 cursor-pointer" onclick="downloadGame()">Descargar</span>
                                    <select name="version" id="version" class="text-lg p-2 bg-branddark text-white rounded-md cursor-pointer hover:bg-branddark-600 transition-all duration-[50ms]"> 
                                        <option value="latest">Última versión</option>
                                    </select>
                                </div>
                                <div id="no-download-avaliable" class="flex flex-row gap-2">
                                    <span class="text-lg">No hay descargas disponibles :(</span>
                                </div>
                                <div class="ml-auto flex flex-row gap-4 bg-branddark-600 p-3 rounded-md">
                                    <a id="game-store-link" href="/store/" class="text-lg link-hover transition-all duration-[50ms]">Tienda</a>
                                    <a id="game-community-link" href="/communities/" class="text-lg link-hover transition-all duration-[50ms]">Comunidad</a>
                                </div>
                            </div>
                            <div id="game-details" class="flex-1">
                                <h2 class="text-2xl">Novedades</h2>
                                <div id="game-news" class="flex flex-col w-full gap-2">

                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="no-games" class="hidden">
                        <h1 class="text-3xl text-center">No tienes juegos en tu biblioteca</h1>
                        <p class="text-center">Puedes adquirir juegos en la <a href="/store" class="text-alt link-hover">tienda</a></p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <style>
        .link-hover {
            position: relative;
        }
        .link-hover::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: -2px;
            width: 100%;
            height: 2px;
            background-color: currentColor;
            transform: scaleX(0);
            transform-origin: bottom right;
            transition: transform 0.3s ease-out;
        }
        .link-hover:hover::after {
            transform: scaleX(1);
            transform-origin: bottom left;
        }
    </style>

    <?php
}

include("views/templates/main.php");

unset($GLOBALS['games']);
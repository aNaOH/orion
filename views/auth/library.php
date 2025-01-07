<?php

$title = "Tu biblioteca en Orion";

function showPage() {
    ?>

    <script src="/assets/js/library.js"></script>

    <!-- App section -->
    <section id="app" class="mt-10 h-full">
        <div class="container h-full">
            <div class="flex flex-row gap-10 h-full">
                <div id="sidebar" class="bg-branddark flex flex-col gap-2 p-5 flex-[0.25]">
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
                    <div id="library-loading">
                        <div class="flex flex-col items-center justify-center h-full">
                            <div class="loader ease-linear rounded-full border-8 border-t-8 border-gray-200 h-32 w-32 mb-4"></div>
                            <h2 class="text-center text-xl">Cargando tu biblioteca...</h2>
                        </div>
                    </div>
                    <div id="game-info">
                        <div class="flex flex-col gap-5">
                            <div id="game-cover" class="w-1/3">
                                <img id="game-image" src="" alt="Cover" class="aspect-[2.14/1] w-full rounded-md shadow-lg">
                            </div>
                            <h1 id="game-title" class="text-3xl">Title</h1>
                            <div id="game-actions" class="flex flex-row gap-2">
                                    <button id="download-game" class="bg-alt hover:bg-alt-600 transition-all p-2 rounded-xl">Descargar</button>
                            </div>
                            <div id="game-details" class="flex-1">
                                <p id="game-description">Description</p>
                                
                            </div>
                        </div>
                    </div>
                    <div id="no-games">
                        <h1 class="text-3xl text-center">No tienes juegos en tu biblioteca</h1>
                        <p class="text-center">Puedes adquirir juegos en la <a href="/store">tienda</a></p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php
}

include("views/templates/nomain.php");

unset($GLOBALS['games']);
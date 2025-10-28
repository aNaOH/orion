<?php

$title = "Orion Store";

if (isset($_SESSION["user"])) {
    $user = User::getById($_SESSION["user"]["id"]);
    $GLOBALS["recommended"] = Recommender::getRecommendations($user);
}

$GLOBALS["randomGames"] = Game::pickRandom(12);

function showPage()
{
    global $randomGames;
    global $recommended;
    $genres = GameGenre::getAll();
    $features = GameFeature::getAll();
    ?>

    <script src="/assets/js/components/gradientChip.js"></script>

   <!-- Hero Section -->
    <section id="hero" class="bg-brand-500 text-white py-20">
    <div class="container mx-auto text-center">
        <h2 class="text-4xl md:text-5xl font-bold animate__animated animate__fadeInDown">Tienda</h2>
        <p class="text-lg md:text-xl mt-4">¡Descubre tu próximo videojuego!</p>
    </div>

    <form id="searchForm" class="flex items-center space-x-2 p-2 rounded-full bg-branddark bg-opacity-75 shadow-md w-[50%] mx-auto">
                <!-- Campo de Búsqueda -->
                <div class="relative flex-grow">
                    <input
                    type="text"
                    name="search"
                    id="search"
                    value="<?= isset($searchQuery)
                        ? htmlspecialchars($searchQuery)
                        : "" ?>"
                    placeholder="¡Mira si lo tenemos aquí!"
                    class="w-full bg-transparent text-text-gray-200 placeholder-text-gray-200/75 px-4 py-2 rounded-full border-none focus:outline-none"
                    >
                    <!-- Icono de Búsqueda -->
                    <div class="absolute inset-y-0 right-3 flex items-center pointer-events-none">
                    <i class="bi bi-search text-white"></i>
                    </div>
                </div>
            </form>
    </section><!-- /Hero Section -->

    <div class="flex flex-row justify-between p-2 w-full mx-auto">
        <form id="filterForm">
            <div class="flex flex-col gap-4">
                <div class="flex flex-col gap-2">
                    <h3 class="text-lg font-semibold mb-2">Género</h3>
                    <select name="genre" class="w-full rounded-full bg-branddark bg-opacity-75 shadow-md text-gray-200 px-4 py-2">
                        <option value="all">Todos</option>
                        <?php foreach ($genres as $genre): ?>
                            <option value="<?= $genre->id ?>"><?= $genre->name ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="flex flex-col gap-2">
                    <h3 class="text-lg font-semibold mb-2">Características</h3>
                    <?php foreach ($features as $feature): ?>
                        <div class="flex items-center">
                            <input type="checkbox" name="feature[]" value="<?= $feature->id ?>" id="feature-<?= $feature->id ?>" class="mr-2">
                            <gradient-chip
                                base-color="<?= $feature->tint ?>"
                                size="12"
                                icon-path="/media/game/feature/<?= $feature->icon ?>"
                                text="<?= $feature->name ?>"
                                border-radius="8">
                            </gradient-chip>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <button type="submit" class="bg-alt text-white px-4 py-2 rounded-md mt-8 w-full hover:bg-alt-600 transition-colors duration-300">Filtrar</button>
        </form>

        <div>
            <?php if (isset($recommended) && count($recommended) > 0) { ?>
                <!-- Recommended Section -->
                <section id="features" class="py-5">
                    <h2 class="text-2xl md:text-3xl font-semibold animate__animated animate__fadeInDown">Pensamos que podría interesarte</h2>

                    <?php if (!isset($games) || count($games) == 0) { ?>
                        <div class="container mx-auto text-center py-16 ">
                        <h1 class="text-3xl md:text-4xl font-bold text-brand-800">No hay juegos...</h1>
                        <p class="text-lg text-gray-600 mt-4">¡Vuelve pronto para descubrir nuevos títulos increíbles!</p>
                        </div>
                    <?php } else { ?>
                        <div class="container mx-auto mt-6">
                            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-8">
                                <?php foreach ($recommended as $game) {
                                    if (!$game->is_public) {
                                        continue;
                                    }
                                    OrionComponents::GameStore($game);
                                } ?>
                            </div>
                        </div>
                    <?php } ?>
                </section>
            <?php } ?>

            <section id="features" class="py-5">
                <h2 class="text-2xl md:text-3xl font-semibold animate__animated animate__fadeInDown">Algunos juegos de nuestro catálogo</h2>

            <?php if (!isset($randomGames) || count($randomGames) == 0) { ?>
                <div class="container mx-auto text-center py-16 ">
                <h1 class="text-3xl md:text-4xl font-bold text-brand-800">No hay juegos...</h1>
                <p class="text-lg text-gray-600 mt-4">¡Vuelve pronto para descubrir nuevos títulos increíbles!</p>
                </div>
            <?php } else { ?>
                <div class="flex flex-col gap-2">
                    <div class="container mx-auto mt-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-8">
                            <?php foreach ($randomGames as $game) {
                                if (!$game->is_public) {
                                    continue;
                                }
                                OrionComponents::GameStore($game);
                            } ?>
                        </div>
                    </div>
                    <a href="/store/games" class="px-8 py-4 bg-alt-500 text-center text-white font-semibold rounded-lg shadow-lg hover:bg-alt-400 focus:ring focus:ring-brand-300 transition">
                    Ver más juegos
                    </a>
                </div>
            <?php } ?>
            </section>
        </div>
    </div>

    <script src="/assets/js/storeFiltering.js"></script>

    <?php
}

include "views/templates/main.php";

unset($GLOBALS["randomGames"]);

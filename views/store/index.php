<?php

$title = $game->title . " | Orion Store";

function getBuyWidget($game)
{
    if (isset($_SESSION["user"])) {
        $user = User::getById($_SESSION["user"]["id"]);

        if (!is_null($user)) {
            if ($user->hasAdquiredGame($game)) { ?>
                <p class="text-lg text-gray-200">Ya tienes este juego</p>
                <a href="/library#game<?= $game->id ?>" class="text-lg font-bold text-gray-200 rounded-xl p-2 bg-alt hover:bg-alt-600 transition-colors duration-300">
                    Ver en la biblioteca
                </a>
            <?php } else { ?>
                <p class="text-lg text-gray-200"><?= $game->base_price > 0
                    ? strval($game->base_price) . " €"
                    : "Gratis" ?> </p>
                <a href="/stripe/game/<?= $game->id ?>" class="text-lg font-bold text-gray-200 rounded-xl p-2 bg-alt hover:bg-alt-600 transition-colors duration-300">
                    <?= $game->base_price > 0 ? "COMPRAR" : "OBTENER" ?>
                </a>
            <?php }
        } else {
             ?>
            <script>
                location.href = '/logout?to=login';
            </script>
        <?php
        }
    } else {
         ?>
        <p class="text-lg text-gray-200"><?= $game->base_price > 0
            ? strval($game->base_price) . " €"
            : "Gratis" ?> </p>
        <a href="/login" class="text-lg font-bold text-gray-200 rounded-xl p-2 bg-alt hover:bg-alt-600 transition-colors duration-300">
            <?= "Inicia sesión para " .
                ($game->base_price > 0 ? "comprarlo" : "obtenerlo") ?>
        </a>
        <?php
    }
}

function showPage()
{
    global $game;
    $gameFeatures = $game->getFeatures();

    global $news;
    $news ??= [];
    ?>

    <script src="/assets/js/components/gradientChip.js"></script>

    <div class="relative py-20 flex justify-center">

    <!-- Store Page -->
    <section id="store" class="max-w-4xl w-full px-6">
        <h1 class="text-2xl font-bold text-gray-200 mx-auto max-w-4xl mb-3"><?= $game->title ?></h1>
      <div class="container mx-auto max-w-4xl bg-branddark shadow-lg rounded-lg p-8">
        <!-- Header -->
        <div class="flex">
          <div class="text-left flex-1">
            <img class="aspect-[2.14/1] h-[65%] rounded-md shadow-lg" src="/media/game/thumb/<?= $game->id ?>" alt="<?= $game->title ?> thumbnail">
            <p class="text-lg text-gray-200"><?= $game->short_description ??
                "No hay información" ?></p>
          </div>
          <div class="flex-1 flex flex-col gap-2 content-between">
            <div class="flex flex-row gap-2 items-center mx-auto">
                <?php getBuyWidget($game); ?>
            </div>
            <div class="text-center flex flex-row">
                <div class="flex-1 flex flex-col">
                    <p class="text-lg font-bold text-gray-200">DESARROLLADOR</p>
                    <p class="text-md text-gray-400"><?= $game->as_editor
                        ? $game->developer_name
                        : $game->getDeveloper()->name ?></p>
                </div>
            <?php if ($game->as_editor) { ?>
                <div class="flex-1 flex flex-col">
                    <p class="text-lg font-bold text-gray-200">EDITOR</p>
                    <p class="text-md text-gray-400"><?= $game->getDeveloper()
                        ->name ?></p>
                </div>
            <?php } ?>
            </div>
          </div>
        </div>

        <!-- Store Details -->
        <div>
          <div class="bg-brand-900 rounded-lg p-6 shadow-sm">
            <div class="flex flex-row gap-4">
                <div id="description container mx-auto p-6">
                    <?php
                    $Parsedown = new TailwindParsedown();
                    echo $Parsedown->text(
                        $game->description ?? "### No hay descripción",
                    );
                    ?>
                </div>
                <?php if (
                    sizeof($game->getAchievements()) > 0 ||
                    sizeof($game->getLeaderboards()) > 0 ||
                    sizeof($game->getStats()) > 0 ||
                    sizeof($gameFeatures) > 0
                ) { ?>
                <div class="flex flex-col gap-2">
                    <p class="text-lg font-bold text-gray-200">CARACTERÍSTICAS</p>
                    <?php foreach ($gameFeatures as $feature) { ?>
                        <gradient-chip
                            base-color="<?= $feature->tint ?>"
                            size="24"
                            icon-path="/media/game/feature/<?= $feature->icon ?>"
                            text="<?= $feature->name ?>"
                            border-radius="8">
                        </gradient-chip>
                    <?php } ?>
                    <?php if (sizeof($game->getAchievements()) > 0) { ?>
                        <gradient-chip
                            base-color="#1B2A49"
                            size="24"
                            icon-path="/media/game/feature/achievement"
                            text="Logros"
                            border-radius="8">
                        </gradient-chip>
                    <?php } ?>
                    <?php if (sizeof($game->getLeaderboards()) > 0) { ?>
                        <gradient-chip
                            base-color="#1B2A49"
                            size="24"
                            icon-path="/media/game/feature/leaderboard"
                            text="Clasificaciones"
                            border-radius="8">
                        </gradient-chip>
                    <?php } ?>
                    <?php if (sizeof($game->getStats()) > 0) { ?>
                        <gradient-chip
                            base-color="#1B2A49"
                            size="24"
                            icon-path="/media/game/feature/stat"
                            text="Estadísticas"
                            border-radius="8">
                        </gradient-chip>
                    <?php } ?>
                </div>
                <?php } ?>
            </div>
          </div>
        </div>
      </div>
    </section>

    <?php if (sizeof($news) > 0) { ?>
            <!-- Noticias pegadas al borde derecho -->
            <aside id="news-section"
                class="hidden lg:block absolute right-[-50px] top-20 w-80 mr-6">
                <div class="rounded-lg p-6">
                    <h3 class="text-2xl font-semibold text-gray-200 mb-4">Noticias</h3>
                    <div class="space-y-4">
                        <?php foreach ($news as $post) {

                            if (!$post->is_public) {
                                continue;
                            }
                            $info = $post->getPostInfo();
                            if (!($info instanceof GameNews)) {
                                continue;
                            }
                            $category = $info->getCategory();
                            ?>
                        <!-- Post Item -->
                        <a href="/communities/<?= $game->id ?>/news/<?= $post->id ?>"
                            class="block bg-branddark rounded-lg shadow-sm p-4 hover:bg-branddark-600 transition-colors duration-300">
                            <div class="flex justify-between items-center">
                                <div class="flex flex-col gap-2">
                                    <h6 class="text-md font-semibold text-gray-200 mb-1"><?= $post->title ?></h6>
                                    <gradient-chip
                                        base-color="<?= $category->tint ?>"
                                        text="<?= $category->name ?>"
                                        border-radius="8">
                                    </gradient-chip>
                                </div>
                                <small class="text-gray-400 text-sm text-right"
                                    data-createdate="<?= $post->created_at->format(
                                        "Y-m-d H:i:s",
                                    ) ?>">
                                    <?= $post->created_at->format("d/m/Y") ?>
                                </small>
                            </div>
                        </a>
                        <?php
                        } ?>
                    </div>
                </div>
            </aside>
        <?php } ?>

    </div>

    <!-- Noticias debajo en móvil -->
    <?php if (sizeof($news) > 0) { ?>
    <section id="news-section-mobile" class="lg:hidden mt-10 px-6 pb-20">
        <h3 class="text-2xl font-semibold text-gray-200 mb-4">Noticias</h3>
        <div class="space-y-4">
            <?php foreach ($news as $post) {

                if (!$post->is_public) {
                    continue;
                }
                $info = $post->getPostInfo();
                if (!($info instanceof GameNews)) {
                    continue;
                }
                $category = $info->getCategory();
                ?>
            <a href="/communities/<?= $game->id ?>/news/<?= $post->id ?>"
                class="block bg-branddark shadow-lg rounded-lg p-6 hover:bg-branddark-600 transition-colors duration-300">
                <div class="flex justify-between items-center">
                    <div class="flex flex-col gap-2">
                        <h6 class="text-lg font-semibold text-gray-200 mb-1"><?= $post->title ?></h6>
                        <gradient-chip
                            base-color="<?= $category->tint ?>"
                            text="<?= $category->name ?>"
                            border-radius="8">
                        </gradient-chip>
                    </div>
                    <small class="text-gray-400 text-sm text-right"
                        data-createdate="<?= $post->created_at->format(
                            "Y-m-d H:i:s",
                        ) ?>">
                        <?= $post->created_at->format("d/m/Y") ?>
                    </small>
                </div>
            </a>
            <?php
            } ?>
        </div>
    </section>
    <?php } ?>

    <script src="/assets/js/addDatesCommunity.js"></script>

    <?php
}

include "views/templates/main.php";

unset($GLOBALS["game"]);

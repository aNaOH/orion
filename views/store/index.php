<?php

$title = $game->title . " | Orion Store";

function getPriceWidget($game)
{
    if ($game->discount > 0 && $game->base_price > 0) { ?>
        <div class="flex flex-row items-center gap-2">
            <p class="text-xs text-gray-200 line-through"><?= $game->base_price ?> €</p>
            <p class="text-lg text-gray-200"><?= $game->getPrice() > 0
                ? strval($game->getPrice()) . " €"
                : "Gratis" ?> </p>
        </div>

        <span class="bg-green-500 text-white px-2 py-1 rounded-md">
            <?= $game->getDiscountText() ?>
        </span>
        <?php } else { ?>
            <p class="text-lg text-gray-200"><?= $game->getPrice() > 0
                ? strval($game->getPrice()) . " €"
                : "Gratis" ?> </p>
        <?php }
}

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
            <?php } else {if (OrderHelper::hasItem($game->id)) { ?>
                <div class="flex flex-col items-center gap-2">
                    <div class="flex flex-row gap-4 items-center">
                        <?php getPriceWidget($game); ?>
                    </div>
                    <span class="text-lg font-bold text-gray-200 rounded-xl p-2 bg-alt-600">
                        En el carrito
                    </span>
                </div>
                <?php } else { ?>
                    <div class="flex flex-col items-center gap-2">
                        <div class="flex flex-row gap-4 items-center">
                            <?php getPriceWidget($game); ?>
                        </div>
                        <button id="cartButton" class="text-lg font-bold text-gray-200 rounded-xl p-2 bg-alt hover:bg-alt-600 transition-colors duration-300">
                            Añadir al carrito
                        </button>
                    </div>
                <?php }}
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
    <section id="store" class="max-w-6xl w-full px-6">
      <div class="container mx-auto max-w-6xl bg-branddark shadow-lg rounded-lg p-8 flex flex-col gap-8">
          <!-- Header -->
          <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                      <!-- THUMB (aspect ratio respetado) -->
                      <div class="w-full">
                          <img
                              src="https://cdn.orion.moonnastd.com/game/thumb/<?= $game->id ?>"
                              alt="<?= $game->title ?> Thumbnail"
                              class="w-full h-auto rounded-lg shadow-md object-contain bg-black/20"
                          >
                      </div>

                      <!-- INFORMACIÓN DEL JUEGO -->
                      <div class="flex flex-col justify-center gap-3">

                          <!-- NOMBRE -->
                          <h1 class="text-3xl font-bold text-white"><?= $game->title ?></h1>

                          <!-- DESARROLLADORA / EDITORA -->
                          <p class="text-gray-300 text-sm leading-tight">
                              <span class="font-semibold text-alt">Desarrolladora:</span> <?= $game->as_editor
                                  ? $game->developer_name
                                  : $game->getDeveloper()->name ?>
                                                          <?php if (
                                                              $game->as_editor
                                                          ) { ?>
                                                              <br>
                                                              <span class="font-semibold text-alt">Editora:</span> <?= $game->getDeveloper()
                                                                  ->name ?>

                                                                      <?php } ?>
                          </p>

                          <!-- FECHA DE LANZAMIENTO -->
                          <p class="text-gray-300 text-sm leading-tight">
                              <span class="font-semibold text-alt">Lanzamiento:</span> <?= date(
                                  "d M Y",
                                  strtotime($game->launch_date),
                              ) ?>
                          </p>

                          <!-- SHORT DESCRIPTION -->
                          <p class="text-gray-200 text-base leading-relaxed">
                              <?= $game->short_description ?>
                          </p>
                      </div>

                      <!-- WIDGET DE COMPRA -->
                      <div class="bg-black/20 rounded-lg p-6 shadow-md">

                          <!-- BOTÓN DE COMPRA (usa tu widget real) -->
                          <div class="flex flex-col items-center justify-center gap-2">
                              <?php getBuyWidget($game); ?>
                          </div>

                      </div>

                  </div>

        <!-- Store Details -->
        <div>
          <div class="rounded-lg p-6">
            <div class="flex flex-row gap-4">
                <div id="description container mx-auto p-6 max-w-4xl">
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
                <div class="flex flex-col gap-2 w-64">
                    <p class="text-lg font-bold text-gray-200">CARACTERÍSTICAS</p>
                    <?php foreach ($gameFeatures as $feature) { ?>
                        <gradient-chip
                            base-color="<?= $feature->tint ?>"
                            size="24"
                            icon-path="https://cdn.orion.moonnastd.com/game/feature/<?= $feature->icon ?>"
                            text="<?= $feature->name ?>"
                            border-radius="8">
                        </gradient-chip>
                    <?php } ?>
                    <?php if (sizeof($game->getAchievements()) > 0) { ?>
                        <gradient-chip
                            base-color="#1B2A49"
                            size="24"
                            icon-path="https://cdn.orion.moonnastd.com/game/feature/achievement"
                            text="Logros"
                            border-radius="8">
                        </gradient-chip>
                    <?php } ?>
                    <?php if (sizeof($game->getLeaderboards()) > 0) { ?>
                        <gradient-chip
                            base-color="#1B2A49"
                            size="24"
                            icon-path="https://cdn.orion.moonnastd.com/game/feature/leaderboard"
                            text="Clasificaciones"
                            border-radius="8">
                        </gradient-chip>
                    <?php } ?>
                    <?php if (sizeof($game->getStats()) > 0) { ?>
                        <gradient-chip
                            base-color="#1B2A49"
                            size="24"
                            icon-path="https://cdn.orion.moonnastd.com/game/feature/stat"
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
    <script>
        document.getElementById("cartButton")?.addEventListener("click", function() {
            $.ajax({
                url: "/api/cart",
                method: "POST",
                data: {
                    gameId: <?= $game->id ?>
                },
                success: function(response) {
                    // refresh the page
                    location.reload();
                },
                error: function(xhr, status, error) {
                    console.error(error);
                }
            });
        });
    </script>

    <?php
}

include "views/templates/main.php";

unset($GLOBALS["game"]);

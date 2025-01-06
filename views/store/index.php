<?php

$title = $game->title." | Orion Store";

function getBuyWidget($game){
    if(isset($_SESSION['user'])) {
        $user = User::getById($_SESSION['user']['id']);

        if(!is_null($user)){
            if($user->hasAdquiredGame($game)) { ?>
                <p class="text-lg text-gray-200">Ya tienes este juego</p>
                <a href="/library#game<?= $game->id ?>" class="text-lg font-bold text-gray-200 rounded-xl p-2 bg-alt hover:bg-alt-600 transition-colors duration-300">
                    Ver en la biblioteca
                </a>
            <?php } else { ?>
                <p class="text-lg text-gray-200"><?= $game->base_price > 0 ? strval($game->base_price) . ' €' : 'Gratis' ?> </p>
                <a href="/stripe/game/<?= $game->id ?>" class="text-lg font-bold text-gray-200 rounded-xl p-2 bg-alt hover:bg-alt-600 transition-colors duration-300">
                    <?= $game->base_price > 0 ? 'COMPRAR' : 'OBTENER' ?>
                </a>
            <?php }
        } else { ?>
            <script>
                location.href = '/logout?to=login';
            </script>
        <?php }
    } else { ?>
        <p class="text-lg text-gray-200"><?= $game->base_price > 0 ? strval($game->base_price) . ' €' : 'Gratis' ?> </p>
        <a href="/login" class="text-lg font-bold text-gray-200 rounded-xl p-2 bg-alt hover:bg-alt-600 transition-colors duration-300">
            <?= 'Inicia sesión para '.($game->base_price > 0 ? 'comprarlo' : 'obtenerlo') ?>
        </a>
        <?php }
}

function showPage() {

  global $game;
    ?>

    <!-- Store Page -->
    <section id="store" class="py-20">
        <h1 class="text-2xl font-bold text-gray-200 mx-auto max-w-4xl mb-3"><?= $game->title ?></h1>
      <div class="container mx-auto max-w-4xl bg-branddark shadow-lg rounded-lg p-8">
        <!-- Header -->
        <div class="flex">
          <div class="text-left flex-1">
            <img class="aspect-[2.14/1] h-[65%] rounded-md shadow-lg" src="/media/game/thumb/<?= $game->id ?>" alt="<?= $game->title ?> thumbnail">
            <p class="text-lg text-gray-200"><?= $game->short_description ?? 'No hay información' ?></p>
          </div>
          <div class="flex-1 flex flex-col gap-2 content-between">
            <div class="flex flex-row gap-2 items-center mx-auto">
                <?php getBuyWidget($game); ?>
            </div>
            <div class="text-center flex flex-row">
                <div class="flex-1 flex flex-col">
                    <p class="text-lg font-bold text-gray-200">DESARROLLADOR</p>
                    <p class="text-md text-gray-400"><?= $game->as_editor ? $game->developer_name : $game->getDeveloper()->name; ?></p>
                </div>
            <?php if($game->as_editor) { ?>
                <div class="flex-1 flex flex-col">
                    <p class="text-lg font-bold text-gray-200">EDITOR</p>
                    <p class="text-md text-gray-400"><?= $game->getDeveloper()->name; ?></p>
                </div>
            <?php } ?>
            </div>
          </div>
        </div>
        
        <!-- Store Details -->
        <div>
          <div class="bg-brand-900 rounded-lg p-6 shadow-sm">
            <div class="flex flex-row gap-4">
                <div id="description container mx-auto p-6 flex-1">
                    <?php
                    $Parsedown = new TailwindParsedown();
                    echo $Parsedown->text($game->description ?? '### No hay descripción');
                    ?>
                </div>
                <?php if(sizeof($game->getAchievements()) > 0 || sizeof($game->getLeaderboards()) > 0 || sizeof($game->getStats()) > 0) { ?>
                <div class="flex-[0.25]">
                    <p class="text-lg font-bold text-gray-200">CARACTERÍSTICAS</p>
                    <?php if(sizeof($game->getAchievements()) > 0) { ?>
                        <div class="bg-brand rounded-lg">
                            Logros
                        </div>
                    <?php } ?>
                    <?php if(sizeof($game->getLeaderboards()) > 0) { ?>
                        <div class="bg-brand rounded-lg">
                            Tablas de clasificación
                        </div>
                    <?php } ?>
                    <?php if(sizeof($game->getStats()) > 0) { ?>
                        <div class="bg-brand rounded-lg">
                            Estadísticas de juego
                        </div>
                    <?php } ?>
                </div>
                <?php } ?>
            </div>
          </div>
        </div>
      </div>
    </section>


    <?php
}

include("views/templates/main.php");

unset($GLOBALS['game']);
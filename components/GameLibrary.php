<div class="bg-branddark flex flex-row gap-5 p-2 rounded-xl" id="game<?= $game->id ?>">
    <img class="aspect-[2.14/1] h-[75px] rounded-md shadow-lg" src="/media/game/thumb/<?= $game->id ?>" alt="<?= $game->title ?> thumbnail">
    <h1 class="text-2xl font-bold text-gray-200"><?= $game->title ?></h1>
    <div class="m-auto flex flex-row gap-5">
        <a href="/store/<?= $game->id ?>" class="text-lg text-gray-200 bg-alt hover:bg-alt-600 rounded-xl p-2 my-auto">Ver en la tienda</a>
        <a href="/communities/<?= $game->id ?>" class="text-lg text-gray-200 bg-alt hover:bg-alt-600 rounded-xl p-2 my-auto">Ver en la comunidad</a>
    </div>
    <div class="ml-auto my-auto">
        <?php if (!is_null($game->getLatestBuild())) { ?>
            <a href="/library/<?= $game->id ?>/<?= urlencode($game->getLatestBuild()->version) ?>" class="text-lg text-gray-200 bg-alt hover:bg-alt-600 rounded-xl p-2" target="_blank" rel="noopener noreferrer">Descargar</a>
        <?php } else { ?>
            <p class="text-lg text-gray-200">No hay descargas disponibles</p>
        <?php } ?>
    </div>
</div>
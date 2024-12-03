<?php

$title = "¡Ups!";

function showPage() {
    global $game;
    global $checkoutinfo;
    ?>

    <!-- 404 Page -->
    <section class="min-h-screen flex flex-col md:flex-row items-center justify-center text-center md:text-left p-8">
    <!-- Content -->
    <div data-aos="fade-right" class="md:w-1/2">
        <!-- Error Title -->
        <h1 class="text-6xl font-bold text-gray-200 mb-4">¡Enhorabuena!</h1>
        <h2 class="text-2xl font-semibold text-gray-400 mb-4">Pago hecho</h2>
        <p class="text-lg text-gray-400 mb-8">
        ¡Disfruta de tu nuevo juego!
        </p>
        <!-- Buttons -->
        <div class="flex justify-center md:justify-start space-x-4">
        <a href="/library#game<?=$game->id?>" class="px-6 py-3 bg-alt text-white font-semibold rounded-lg shadow-lg hover:bg-alt-400 focus:ring focus:ring-brand-300 transition">
            Verlo en la biblioteca
        </a>
        </div>
    </div>
    <!-- Decorative Image -->
    <div data-aos="fade-left" class="md:w-1/2 flex flex-col justify-center mt-12 md:mt-0">
        <h2 class="text-2xl font-semibold text-gray-400 mb-4">Tu compra</h2>
        <div class="bg-branddark flex flex-row gap-5 p-2 rounded-xl">
            <img class="aspect-[2.14/1] h-[75px] rounded-md shadow-lg" src="/media/game/thumb/<?= $game->id ?>" alt="<?= $game->title ?> thumbnail">
            <h1 class="text-2xl font-bold text-gray-200"><?= $game->title ?></h1>
            <div class="flex flex-col gap-2 flex-1 text-right">
                <p class="text-lg text-gray-200">
                    <span class="<?= $checkoutinfo['price'] > 0 && $checkoutinfo['discountedAmount'] > 0 ? 'line-through' : '' ?>"><?= $checkoutinfo['price'] > 0 ? strval($checkoutinfo['price']) . ' €' : 'Gratis' ?></span>
                    <span class="no-underline"><?= $checkoutinfo['price'] > 0 && $checkoutinfo['discountedAmount'] > 0 ? $checkoutinfo['price']-$checkoutinfo['discountedAmount'].' €' : '' ?></span>
                </p>
                <?php if ($checkoutinfo['price'] > 0 && $checkoutinfo['discountedAmount'] > 0) { ?>
                    <p class="text-lg text-gray-900 bg-green-600 rounded-xl p-2 ml-auto"><?= $checkoutinfo['discount'] ?>% de descuento</p>
                <?php } ?>
            </div>
        </div>
    </div>
    </section>

    <?php
}

include("views/templates/nomain.php");

unset($GLOBALS['game']);
unset($GLOBALS['checkoutinfo']);
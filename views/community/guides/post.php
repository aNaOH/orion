<?php

$title = "Guía '$post->title' para $game->title en Orion";

function showPage() {
    global $game;
    global $post;
    $guide = $post->getPostInfo();
    $gType = $guide->getType();
    ?>

    <script src="/assets/js/components/gradientChip.js"></script>

    <!-- Hero Section -->
    <section id="hero" class="bg-brand-500 text-white py-20">
    <div class="container mx-auto text-center">
        <h2 class="text-4xl md:text-5xl font-bold animate__animated animate__fadeInDown">
        <?= $post->title ?> 
        </h2>
        <p class="mt-4 text-xl">Guía para <a href="/communities/<?= $game->id ?>" class="font-semibold hover:text-gray-300 link-underline"><?= $game->title ?></a></p>
        <p class="text-lg mt-4 flex flex-row gap-x-3 justify-center">
            <span>Escrita por <a href="/profile/<?= $post->getAuthor()->id ?>" class="font-semibold hover:text-gray-300 link-underline"><?= $post->getAuthor()->username ?></a></span>
            <gradient-chip 
                base-color="<?= $gType->tint ?>" 
                size="15" 
                icon-path="/media/guidetype/<?= $gType->icon ?>" 
                text="<?= $gType->type ?>" 
                border-radius="8">
            </gradient-chip> 
        </p>
    </div>
    </section><!-- /Hero Section -->

    <!-- Features Section -->
    <section id="features" class="py-6 rounded-t-xl bg-branddark">
    <div class="container mx-auto p-6">
        <?php
        $Parsedown = new TailwindParsedown();
        echo $Parsedown->text($post->body);
        ?>
    </div>
    </section><!-- /Features Section -->

    <?php
}

include("views/templates/main.php");

unset($GLOBALS['game']);
unset($GLOBALS['post']);
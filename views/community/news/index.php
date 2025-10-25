<?php

$title = "Noticias de $game->title en Orion";

function showPage()
{
    global $game;
    global $posts;
    ?>

    <script src="/assets/js/components/gradientChip.js"></script>

    <!-- Hero Section -->
    <section id="hero" class="bg-brand-500 text-white py-20">
    <div class="container mx-auto text-center">
        <h2 class="text-4xl md:text-5xl font-bold animate__animated animate__fadeInDown">Noticias de <?= $game->title ?></h2>
    </div>
    </section><!-- /Hero Section -->

    <!-- Features Section -->
    <section id="features" class="py-20">
    <div class="container mx-auto">
        <div class="space-y-4">
        <?php foreach ($posts as $post) {

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
            <a href="/communities/<?= $game->id ?>/news/<?= $post->id ?>" class="block bg-branddark shadow-lg rounded-lg p-6 hover:bg-branddark-600 transition-colors duration-300">
            <div class="flex justify-between items-center">
                <div class="flex flex-col gap-2">
                    <h6 class="text-lg font-semibold text-gray-200 mb-1"><?= $post->title ?></h6>
                    <gradient-chip
                        base-color="<?= $category->tint ?>"
                        text="<?= $category->name ?>"
                        border-radius="8">
                    </gradient-chip>
                </div>
                <small class="text-gray-400 text-sm" data-createdate="<?= $post->created_at->format(
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
    </section><!-- /Features Section -->


    <script src="/assets/js/addDatesCommunity.js"></script>

    <?php
}

include "views/templates/main.php";

unset($GLOBALS["game"]);
unset($GLOBALS["posts"]);

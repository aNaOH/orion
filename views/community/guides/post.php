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
    <section id="features" class="py-6 rounded-xl bg-branddark">
    <div class="container mx-auto p-6">
        <?php
        $Parsedown = new TailwindParsedown();
        echo $Parsedown->text($post->body);
        ?>
    </div>
    </section><!-- /Features Section -->

    <!-- Features Section -->
    <section id="comments" class="my-4 py-6 rounded-xl bg-branddark">
        <h2 class="text-xl md:text-2xl mx-6 font-bold">Comentarios</h2>
        <div class="container mx-auto p-6 flex flex-col gap-5">
            <?php if(isset($_SESSION['user'])) { ?>

                <form id="commentForm" method="post" action="/api/communities/comment/<?= $post->id ?>">
                    <?php OrionComponents::TokenInput(ETOKEN_TYPE::USERACTION) ?>
                    <div class="bg-branddark-600 flex flex-row p-2 gap-5 rounded-xl">
                        <div class="w-16 h-16 rounded-full overflow-hidden border-4 border-alt-500">
                            <img src="/media/profile/<?= $_SESSION['user']['profile_pic'] ?? 'default' ?>" alt="Foto de perfil de <?= $_SESSION['user']['username'] ?>" class="w-full h-full object-cover">
                        </div>
                        <div class="flex flex-col gap-2 w-full mr-4">
                            <p class="font-semibold text-gray-200">Comentando como <?= $_SESSION['user']['username'] ?></p>
                            <textarea name="comment" id="comment" style="resize: none;" require placeholder="Escribe aquí tu comentario" class="p-2 rounded-xl text-gray-200 w-full bg-branddark"></textarea>
                            <button class="ml-auto px-3 py-1.5 bg-alt-500 text-white font-semibold rounded-lg shadow-lg hover:bg-alt-400 focus:ring focus:ring-brand-300 transition" id="submitButton" type="submit">
                                Comentar
                            </button>
                        </div>
                    </div>
                </form>

            <?php } foreach ($post->getComments() as $comment) { ?>
                <div class="bg-branddark-600 flex flex-row p-2 gap-5 rounded-xl">
                        <div class="w-16 h-16 rounded-full overflow-hidden border-4 border-alt-500">
                            <img src="/media/profile/<?= $comment->getAuthor()->profile_pic ?? 'default' ?>" alt="Foto de perfil de <?= $comment->getAuthor()->username ?>" class="w-full h-full object-cover">
                        </div>
                        <div class="flex flex-col gap-2 w-full mr-4">
                            <p class="font-semibold text-gray-200"><?= $comment->getAuthor()->username ?></p>
                            <p class="text-gray-200 w-full"><?= $comment->body ?></p>   
                    </div>
                </div>
            <?php } ?>
        </div>
    </section><!-- /Features Section -->

    <?php
}

include("views/templates/main.php");

unset($GLOBALS['game']);
unset($GLOBALS['post']);
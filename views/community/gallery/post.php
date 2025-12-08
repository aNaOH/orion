<?php

$title = "Post '$post->title' para $game->title en Orion";

function showPage()
{
    global $game;
    global $post;
    ?>

    <script src="/assets/js/components/galleryvote.js"></script>

    <!-- Features Section -->
    <section id="features" class="py-20">
    <div class="container mx-auto p-6">
        <?php OrionComponents::GalleryEntry($post); ?>
    </div>
    </section><!-- /Features Section -->

    <!-- Features Section -->
    <section id="comments" class="my-4 py-6 rounded-xl bg-branddark">
        <h2 class="text-xl md:text-2xl mx-6 font-bold">Comentarios</h2>
        <div class="container mx-auto p-6 flex flex-col gap-5">
            <?php
            if (isset($_SESSION["user"])) { ?>

                <form id="commentForm" method="post" action="/api/communities/comment/<?= $post->id ?>">
                    <?php OrionComponents::TokenInput(
                        ETOKEN_TYPE::USERACTION,
                    ); ?>
                    <div class="bg-branddark-600 flex flex-row p-2 gap-5 rounded-xl">
                        <div class="w-16 h-16 rounded-full overflow-hidden border-4 border-alt-500">
                            <img src="https://cdn.orion.moonnastd.com/profile/<?= $_SESSION[
                                "user"
                            ]["profile_pic"] ??
                                "default" ?>" alt="Foto de perfil de <?= $_SESSION[
    "user"
]["username"] ?>" class="w-full h-full object-cover">
                        </div>
                        <div class="flex flex-col gap-2 w-full mr-4">
                            <p class="font-semibold text-gray-200">Comentando como <?= $_SESSION[
                                "user"
                            ]["username"] ?></p>
                            <textarea name="comment" id="comment" style="resize: none;" require placeholder="Escribe aquí tu comentario" class="p-2 rounded-xl text-gray-200 w-full bg-branddark"></textarea>
                            <button class="ml-auto px-3 py-1.5 bg-alt-500 text-white font-semibold rounded-lg shadow-lg hover:bg-alt-400 focus:ring focus:ring-brand-300 transition" id="submitButton" type="submit">
                                Comentar
                            </button>
                        </div>
                    </div>
                </form>

            <?php }
            foreach ($post->getComments() as $comment) {
                OrionComponents::Comment($comment);
            }?>
        </div>
    </section><!-- /Features Section -->

    <script src="/assets/js/galleryPost.js"></script>

    <?php
}

include "views/templates/main.php";

unset($GLOBALS["game"]);
unset($GLOBALS["post"]);

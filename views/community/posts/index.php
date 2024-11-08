<?php

$title = "Posts de $game->title en Orion";

function showPage() {
    global $game;
    global $posts;
    ?>

    <!-- Hero Section -->
    <section id="hero" class="hero section dark-background">

        <div class="carousel-container">
            <h2 class="animate__animated animate__fadeInDown">Posts de <?= $game->title ?></h2>
            <?php if (isset($_SESSION['user']['id'])) { ?>
                <div class="d-flex flex-row justify-content-between">
                    <a href="/communities/<?= $game->id ?>/posts/create" class="btn-get-started animate__animated animate__fadeInUp scrollto">Nuevo post</a>
                </div>
            <?php } ?>
          </div>

    </section><!-- /Hero Section -->

    <!-- Features Section -->
    <section id="features" class="features section">

    

        <div class="container">

            <div class="list-group">
                <?php foreach ($posts as $post) { 
                    
                    if(!$post->is_public) continue;

                    ?>

                        <a href="/communities/<?=$game->id?>/posts/<?=$post->id?>" class="list-group-item list-group-item-action d-flex gap-3 py-3" aria-current="true">
                            <div class="d-flex gap-2 w-100 justify-content-between">
                                <div>
                                    <h6 class="mb-0"><?=$post->title?></h6>
                                    <p class="mb-0 opacity-75">de <?=$post->getAuthor()->username?></p>
                                </div>
                                <small class="opacity-50 text-nowrap" data-createdat="<?= $post->created_at->format('Y-m-d H:i:s') ?>"></small>

                            </div>
                        </a>
                    
                <?php } ?>
                
            </div>

        </div>

    </section><!-- /Features Section -->


    <script src="/assets/js/addDatesCommunity.js"></script>

    <?php
}

include("views/templates/main.php");

unset($GLOBALS['game']);
unset($GLOBALS['posts']);
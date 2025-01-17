<?php

$title = "Editar comunidad para ".$game->title." | Orion Dev Panel";


//$hasTable = "/admin/js/tables/dev/games.js";

function showPage() {
    global $game;
    ?>

<script src="/assets/js/components/fileUpload.js"></script>


<div class="app-content-header"> <!--begin::Container-->
                <div class="container-fluid"> <!--begin::Row-->
                    <div class="row">
                        <div class="col-sm-6">
                            <h3 class="mb-0">Editar comunidad para <?=$game->title?></h3>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-end">
                                <li class="breadcrumb-item">Inicio</li>
                                <li class="breadcrumb-item">Juegos</li>
                                <li class="breadcrumb-item"><?= $game->title ?></li>
                                <li class="breadcrumb-item active" aria-current="page">Comunidad</li>
                            </ol>
                        </div>
                    </div> <!--end::Row-->
                </div> <!--end::Container-->
            </div> <!--end::App Content Header--> <!--begin::App Content-->
            <div class="app-content"> <!--begin::Container-->
                <div class="container-fluid"> <!--begin::Row-->
                    
                </div> <!--end::Container-->
            </div> <!--end::App Content-->

            <!-- Include Bootstrap JS and CSS -->
            <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
            <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
            <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

            <?php
}

include("views/dev/panel/template/main.php");
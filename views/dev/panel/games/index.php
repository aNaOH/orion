<?php

$title = "Tus juegos | Orion Dev Panel";

$hasTable = "/admin/js/tables/dev/games.js";

function showPage() {
    global $games;
    ?>

<div class="app-content-header"> <!--begin::Container-->
                <div class="container-fluid"> <!--begin::Row-->
                    <div class="row">
                        <div class="col-sm-6">
                            <h3 class="mb-0">Tus juegos</h3>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-end">
                                <li class="breadcrumb-item">Inicio</li>
                                <li class="breadcrumb-item">Juegos</li>
                                <li class="breadcrumb-item active" aria-current="page">Tus juegos</li>
                            </ol>
                        </div>
                    </div> <!--end::Row-->
                </div> <!--end::Container-->
            </div> <!--end::App Content Header--> <!--begin::App Content-->
            <div class="app-content"> <!--begin::Container-->
                <div class="container-fluid"> <!--begin::Row-->
                    <a href="/dev/panel/games/new" class="btn btn-primary">Nuevo juego</a>
                    
                    <table id="table" style="width:100%">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Título</th>
                                <th>Precio base</th>
                                <th>Descuento</th>
                                <th>¿Como editora?</th>
                                <th>Desarrolladora</th>
                                <th>Género</th>
                                <th>Tienda</th>
                                <th>Comunidad</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($games as $game) { ?>
                                <tr>
                                    <th><?= $game->id ?></th>
                                    <td><?= $game->title ?></td>
                                    <td><?= $game->base_price == 0 ? 'Gratis' : strval($game->base_price).' €' ?></td>
                                    <td><?= $game->discount == 0 ? 'No' : strval($game->discount * 100).' %' ?></td>
                                    <td><?= $game->as_editor ? 'Si' : 'No' ?></td>
                                    <td><?= $game->as_editor ? $game->developer_name : $game->getDeveloper()->name ?></td>
                                    <td><?= $game->getGenre() ? $game->getGenre()->name : 'N/A' ?></td>
                                    <td><?= $game->is_public ? '<a href="/store/'.strval($game->id).'">Ir a la tienda</a>' : 'No está disponible' ?></td>
                                    <td><?= $game->is_public ? '<a href="/communities/'.strval($game->id).'">Ir a la comunidad</a>' : 'No está disponible' ?></td>
                                    <td>
                                        <a href="/dev/panel/games/<?=$game->id?>/store/" class="btn btn-primary">Editar tienda</a>
                                        <!-- <a href="/dev/panel/games/<?=$game->id?>/community/" class="btn btn-warning">Editar comunidad</a> -->
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
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
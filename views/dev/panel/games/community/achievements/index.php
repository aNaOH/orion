<?php

$title = "Logros para ".$game->title." | Orion Dev Panel";

$hasTable = "/admin/js/tables/dev/achievements.js";


function showPage() {
    global $game;
    global $achievements;
    ?>

<div class="app-content-header"> <!--begin::Container-->
                <div class="container-fluid"> <!--begin::Row-->
                    <div class="row">
                        <div class="col-sm-6">
                            <h3 class="mb-0">Logros para <?= $game->title ?></h3>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-end">
                                <li class="breadcrumb-item">Inicio</li>
                                <li class="breadcrumb-item">Juegos</li>
                                <li class="breadcrumb-item"><?= $game->title ?></li>
                                <li class="breadcrumb-item">Comunidad</li>
                                <li class="breadcrumb-item active" aria-current="page">Logros</li>
                            </ol>
                        </div>
                    </div> <!--end::Row-->
                </div> <!--end::Container-->
            </div> <!--end::App Content Header--> <!--begin::App Content-->
            <div class="app-content"> <!--begin::Container-->
                <div class="container-fluid"> <!--begin::Row-->
                    <a href="/dev/panel/games/<?= $game->id ?>/community/achievements/new" class="btn btn-primary">Nuevo logro</a>
                    
                    <table id="table" style="width:100%">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Descripción</th>
                                <th>Tipo</th>
                                <th>Estadística asociada</th>
                                <th>¿Secreto?</th>
                                <th>Icono</th>
                                <th>Icono (bloqueado)</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($achievements as $achievement) { ?>
                                <tr>
                                    <th><?= $achievement->id ?></th>
                                    <td><?= $achievement->name ?></td>
                                    <td><?= $achievement->description ?></td>
                                    <td><?= $achievement->type == EACHIEVEMENT_TYPE::TRIGGERED ? 'Por activación' : 'Por estadística' ?></td>
                                    <td><?= $achievement->type == EACHIEVEMENT_TYPE::STAT && !is_null($achievement->stat_id) ? $achievement->stat_id : 'No' ?></td>
                                    <td><?= $achievement->secret ? 'Si' : 'No' ?></td>
                                    <td><?= $achievement->icon ?></td>
                                    <td><?= $achievement->locked_icon ?></td>
                                    <td>
                                        <a href="/dev/panel/games/<?= $game->id ?>/community/achievements/<?=$achievement->id?>/edit/" class="btn btn-primary">Tienda</a>
                                        <a href="/dev/panel/games/<?=$game->id?>/community/achievements/<?=$achievement->id?>/delete/" class="btn btn-primary">Comunidad</a>
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
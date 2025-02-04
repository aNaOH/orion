<?php

$title = "Nuevo logro | Orion Dev Panel";

function showPage() {
    global $game;
    global $stats;
    ?>

<script src="/assets/js/components/fileUpload.js"></script>

<div class="app-content-header"> <!--begin::Container-->
                <div class="container-fluid"> <!--begin::Row-->
                    <div class="row">
                        <div class="col-sm-6">
                            <h3 class="mb-0">Nuevo logro</h3>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-end">
                            <li class="breadcrumb-item">Inicio</li>
                                <li class="breadcrumb-item">Juegos</li>
                                <li class="breadcrumb-item"><?= $game->title ?></li>
                                <li class="breadcrumb-item">Comunidad</li>
                                <li class="breadcrumb-item">Logros</li>
                                <li class="breadcrumb-item active" aria-current="page">Nuevo</li>
                            </ol>
                        </div>
                    </div> <!--end::Row-->
                </div> <!--end::Container-->
            </div> <!--end::App Content Header--> <!--begin::App Content-->
            <div class="app-content"> <!--begin::Container-->
                <div class="container-fluid"> <!--begin::Row-->
                    <form id="newAchievementForm">
                        <?php OrionComponents::TokenInput(ETOKEN_TYPE::DEVACTION, [
                            'userID' => $_SESSION['user']['id'],
                            'gameID' => $game->id
                        ]) ?>
                        <input type="hidden" name="game" value="<?= $game->id ?>">
                        <div class="form-floating mb-3">
                            <input class="form-control" name="name" id="name" type="text" placeholder="Nombre" />
                            <label for="name">Nombre</label>
                            <div class="invalid-feedback" id="nameError"></div>
                        </div>
                        <div class="form-floating mb-3">
                            <input class="form-control" id="description" name="description" type="text" placeholder="Descripción" />
                            <label for="description">Descripción</label>
                            <div class="invalid-feedback" id="descriptionError"></div>
                        </div>
                        <div class="form-floating mb-3">
                            <select class="form-control" id="type" name="type">
                                <option value="0">Por activación</option>
                                <option value="1">Por estadística</option>
                            </select>
                            <label for="type">Tipo</label>
                            <div class="invalid-feedback" id="typeError"></div>
                        </div>
                        <div class="d-none form-floating mb-3" id="statContainer">
                            <select class="form-control" id="stat" name="stat">
                                <option value="-1">Selecciona una estadística</option>
                                <?php foreach ($stats as $stat) { ?>
                                    <option value="<?= $stat->id ?>"><?= $stat->name ?></option>
                                <?php } ?>
                            </select>
                            <label for="stat">Estadística asociada</label>
                            <div class="invalid-feedback" id="statError"></div>
                        </div>
                        <div class="form-floating mb-3">
                            <file-upload id="icon" name="icon" accept-image="true" accept-video="false" min-image-width="64" max-image-width="64" min-image-height="64" max-image-height="64" max-image-size="1MB" image-type="png"></file-upload>
                            <label for="icon">Icono</label>
                            <div class="invalid-feedback" id="iconError"></div>
                        </div>
                        <div class="form-floating mb-3">
                            <file-upload id="lockedIcon" name="lockedIcon" accept-image="true" accept-video="false" min-image-width="64" max-image-width="64" min-image-height="64" max-image-height="64" max-image-size="1MB" image-type="png"></file-upload>
                            <label for="lockedIcon">Icono (bloqueado) - Opcional</label>
                            <div class="invalid-feedback" id="lockedIconError"></div>
                        </div>
                        <div class="d-grid">
                            <button class="btn btn-primary btn-lg" id="submitButton" type="submit">Crear</button>
                            <div class="invalid-feedback" id="submitError"></div>
                        </div>
                    </form>
                </div> <!--end::Container-->
            </div> <!--end::App Content-->

            <script src="/assets/js/forms/dev/getStoreID.js"></script>
            <script src="/assets/js/forms/validator.js"></script>
            <script src="/assets/js/forms/dev/achievement.js"></script>

            <?php
}

include("views/dev/panel/template/main.php");
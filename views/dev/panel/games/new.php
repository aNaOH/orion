<?php

$title = "Nuevo juego | Orion Dev Panel";

function showPage() {
    ?>

<div class="app-content-header"> <!--begin::Container-->
                <div class="container-fluid"> <!--begin::Row-->
                    <div class="row">
                        <div class="col-sm-6">
                            <h3 class="mb-0">Nuevo juego</h3>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-end">
                                <li class="breadcrumb-item">Inicio</li>
                                <li class="breadcrumb-item">Juegos</li>
                                <li class="breadcrumb-item active" aria-current="page">Nuevo juego</li>
                            </ol>
                        </div>
                    </div> <!--end::Row-->
                </div> <!--end::Container-->
            </div> <!--end::App Content Header--> <!--begin::App Content-->
            <div class="app-content"> <!--begin::Container-->
                <div class="container-fluid"> <!--begin::Row-->
                    <form id="newGameForm">
                        <div class="form-floating mb-3">
                            <input class="form-control" id="title" id="name" type="text" placeholder="Título" />
                            <label for="title">Título</label>
                            <div class="invalid-feedback" id="titleError"></div>
                        </div>
                        <div class="form-floating mb-3">
                            <input class="form-control" id="shortDescription" name="shortDescription" type="text" placeholder="Descripción corta" />
                            <label for="shortDescription">Descripción corta</label>
                            <div class="invalid-feedback" id="shortDescriptionError"></div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label d-block"></label>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" id="asEditor" type="checkbox" name="asEditor" />
                                <label class="form-check-label" for="asEditor">¿Eres la editora?</label>
                            </div>
                        </div>
                        <div class="d-none form-floating mb-3" id="developerNameContainer">
                            <input class="form-control" id="developerName" name="developerName" type="text" placeholder="Desarrollador"/>
                            <label for="developerName">Desarrollador</label>
                            <div class="invalid-feedback" id="developerNameError"></div>
                        </div>
                        <div class="d-grid">
                            <button class="btn btn-primary btn-lg" id="submitButton" type="submit">Crear</button>
                        </div>
                    </form>
                </div> <!--end::Container-->
            </div> <!--end::App Content-->

            <script src="/assets/js/forms/validator.js"></script>
            <script src="/assets/js/forms/dev/game.js"></script>

            <?php
}

include("views/dev/panel/template/main.php");
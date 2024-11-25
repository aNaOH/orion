<?php

$title = "Bienvenido a Orion";

function showPage() {
    ?>

<div class="app-content-header"> <!--begin::Container-->
    <div class="container-fluid"> <!--begin::Row-->
        <div class="row">
            <div class="col-sm-6">
                <h3 class="mb-0">Crear tipo de guía</h3>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-end">
                    <li class="breadcrumb-item"><a href="/admin/">Inicio</a></li>
                    <li class="breadcrumb-item active" aria-current="page">
                    Crear tipo de guía
                    </li>
                </ol>
            </div>
        </div> <!--end::Row-->
    </div> <!--end::Container-->
</div> <!--end::App Content Header--> <!--begin::App Content-->
<div class="app-content"> <!--begin::Container-->
    <div class="container-fluid"> <!--begin::Row-->
        <div class="card card-primary card-outline mb-4"> <!--begin::Header-->
            <form id="guideTypeForm"> <!--begin::Body-->
                <div class="card-body">
                    <div class="row mb-3"> <label for="type" class="col-sm-2 col-form-label">Nombre</label>
                        <div class="col-sm-10"> <input type="text" class="form-control" id="type" name="type"> </div>
                    </div>
                </div> <!--end::Body--> <!--begin::Footer-->
                <div class="card-footer"> <button type="submit" id="submitButton" class="btn btn-primary">Crear juego</button> </div> <!--end::Footer-->
            </form> <!--end::Form-->
        </div> <!--end::Horizontal Form-->
    </div> <!--end::Container-->
</div> <!--end::App Content-->

<script src="/assets/js/forms/validator.js"></script>
<script src="/assets/js/forms/admin/quickgame.js"></script>
            <?php
}

include("views/admin/template/main.php");
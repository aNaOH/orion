<?php

$title = "Editar tienda para ".$game->title." | Orion Dev Panel";

function showPage() {
    global $game;
    ?>

<link rel="stylesheet" href="/assets/vendor/simplemde/simplemde.min.css">
<script src="/assets/vendor/simplemde/simplemde.min.js"></script>

<script src="/assets/js/components/fileUpload.js"></script>

<div class="app-content-header"> <!--begin::Container-->
                <div class="container-fluid"> <!--begin::Row-->
                    <div class="row">
                        <div class="col-sm-6">
                            <h3 class="mb-0">Editar tienda para <?=$game->title?></h3>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-end">
                                <li class="breadcrumb-item">Inicio</li>
                                <li class="breadcrumb-item">Juegos</li>
                                <li class="breadcrumb-item"><?= $game->title ?></li>
                                <li class="breadcrumb-item active" aria-current="page">Tienda</li>
                            </ol>
                        </div>
                    </div> <!--end::Row-->
                </div> <!--end::Container-->
            </div> <!--end::App Content Header--> <!--begin::App Content-->
            <div class="app-content"> <!--begin::Container-->
                <div class="container-fluid"> <!--begin::Row-->
                    <div class="container mt-4">
                        <!-- Tabs -->
                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="store-tab" data-bs-toggle="tab" data-bs-target="#store" type="button" role="tab" aria-controls="store" aria-selected="true">
                                    Página de la tienda
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="builds-tab" data-bs-toggle="tab" data-bs-target="#builds" type="button" role="tab" aria-controls="builds" aria-selected="false">
                                    Compilaciones
                                </button>
                            </li>
                        </ul>

                        <!-- Tab Content -->
                        <div class="tab-content mt-3" id="myTabContent">
                            <div class="tab-pane fade show active" id="store" role="tabpanel" aria-labelledby="store-tab">
                                <h3>Página de la tienda</h3>
                                <form id="editGameForm">
                                    <div class="form-floating mb-3">
                                        <input class="form-control" id="title" name="title" type="text" placeholder="Título" value="<?=$game->title?>" />
                                        <label for="title">Título</label>
                                        <div class="invalid-feedback" id="titleError"></div>
                                    </div>
                                    <div class="form-floating mb-3">
                                        <input class="form-control" id="shortDescription" name="shortDescription" type="text" placeholder="Descripción corta" value="<?=$game->short_description?>" />
                                        <label for="shortDescription">Descripción corta</label>
                                        <div class="invalid-feedback" id="shortDescriptionError"></div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label d-block"></label>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" id="asEditor" type="checkbox" name="asEditor" <?= $game->as_editor ? 'checked' : '' ?> />
                                            <label class="form-check-label" for="asEditor">¿Eres la editora?</label>
                                        </div>
                                    </div>
                                    <div class="d-none form-floating mb-3" id="developerNameContainer">
                                        <input class="form-control" id="developerName" name="developerName" type="text" placeholder="Desarrollador" value="<?=$game->developer_name?>"/>
                                        <label for="developerName">Desarrollador</label>
                                        <div class="invalid-feedback" id="developerNameError"></div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label d-block" for="price">Precio</label>
                                        <select class="form-control" id="price" name="price">
                                            <option value="0" <?= is_null($game->base_price) || $game->base_price == 0 ? 'selected' : '' ?>>Gratis</option>
                                            <option value="1.99" <?= $game->base_price == 1.99 ? 'selected' : '' ?>>1,99 €</option>
                                            <option value="2.99" <?= $game->base_price == 2.99 ? 'selected' : '' ?>>2,99 €</option>
                                            <option value="3.99" <?= $game->base_price == 3.99 ? 'selected' : '' ?>>3,99 €</option>
                                            <option value="4.99" <?= $game->base_price == 4.99 ? 'selected' : '' ?>>4,99 €</option>
                                            <option value="5.99" <?= $game->base_price == 5.99 ? 'selected' : '' ?>>5,99 €</option>
                                            <option value="6.99" <?= $game->base_price == 6.99 ? 'selected' : '' ?>>6,99 €</option>
                                            <option value="7.99" <?= $game->base_price == 7.99 ? 'selected' : '' ?>>7,99 €</option>
                                            <option value="8.99" <?= $game->base_price == 8.99 ? 'selected' : '' ?>>8,99 €</option>
                                            <option value="9.99" <?= $game->base_price == 9.99 ? 'selected' : '' ?>>9,99 €</option>
                                            <option value="14.99" <?= $game->base_price == 14.99 ? 'selected' : '' ?>>14,99 €</option>
                                            <option value="19.99" <?= $game->base_price == 19.99 ? 'selected' : '' ?>>19,99 €</option>
                                            <option value="24.99" <?= $game->base_price == 24.99 ? 'selected' : '' ?>>24,99 €</option>
                                            <option value="29.99" <?= $game->base_price == 29.99 ? 'selected' : '' ?>>29,99 €</option>
                                            <option value="39.99" <?= $game->base_price == 39.99 ? 'selected' : '' ?>>39,99 €</option>
                                            <option value="49.99" <?= $game->base_price == 49.99 ? 'selected' : '' ?>>49,99 €</option>
                                            <option value="59.99" <?= $game->base_price == 59.99 ? 'selected' : '' ?>>59,99 €</option>
                                            <option value="69.99" <?= $game->base_price == 69.99 ? 'selected' : '' ?>>69,99 €</option>
                                            <option value="79.99" <?= $game->base_price == 79.99 ? 'selected' : '' ?>>79,99 €</option>
                                        </select>
                                        <div class="invalid-feedback" id="priceError"></div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label d-block" for="discount">Descuento</label> 
                                        <div class="input-group">
                                            <input 
                                                class="form-control" 
                                                id="discount" 
                                                name="discount" 
                                                type="number" 
                                                min="0" 
                                                max="100" 
                                                step="1" 
                                                placeholder="Introduce un porcentaje" 
                                                value="<?=$game->discount * 100?>" 
                                            />
                                            <span class="input-group-text">%</span>
                                        </div>
                                        <div class="invalid-feedback" id="discountError"></div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label d-block" for="description">Descripción</label>
                                        <textarea name="description" id="description" rows="8" placeholder="Escribe el contenido aquí..." required><?=$game->description?></textarea>
                                        <div class="invalid-feedback" id="descriptionError"></div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label d-block" for="coverFile">Portada (Una imagen de 600x900)</label>
                                        <file-upload id="coverFile" min-image-width="600" max-image-width="600" min-image-height="900" max-image-height="900"></file-upload>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label d-block" for="thumbFile">Miniatura (Una imagen de 920x430)</label>
                                        <file-upload id="thumbFile" min-image-width="920" max-image-width="920" min-image-height="430" max-image-height="430"></file-upload>
                                    </div>
                                    <div class="d-grid">
                                        <button class="btn btn-primary btn-lg" id="submitButtonEdit" type="submit">Cambiar</button>
                                        <button class="btn btn-warning btn-lg" data-status="<?=$game->is_public ? 'public' : 'hidden'?>" id="changeVisibility"><?= $game->is_public ? 'Ocultar' : 'Publicar' ?></button>
                                    </div>
                                </form>
                            </div>
                            <div class="tab-pane fade" id="builds" role="tabpanel" aria-labelledby="builds-tab">
                                <h3>Compilaciones</h3>
                                <form id="buildForm">
                                    <div class="form-floating mb-3">
                                        <input class="form-control" id="version" name="version" type="text" placeholder="Versión" />
                                        <label for="version">Versión</label>
                                        <div class="invalid-feedback" id="versionError"></div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="file">Compilación</label>
                                        <input class="form-control" id="file" name="file" type="file" accept="application/zip" />
                                        <div class="invalid-feedback" id="versionError"></div>
                                    </div>
                                    <div class="d-grid">
                                        <button class="btn btn-primary btn-lg" id="submitButtonBuild" type="submit">Subir</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div> <!--end::Container-->
            </div> <!--end::App Content-->

            <script>
                var simplemde = new SimpleMDE({ 
                    element: document.getElementById("description"),
                    autosave: {
                        enabled: true,
                        uniqueId: "Orion_StoreGame_<?=$game->id?>_Description",
                        delay: 1000,
                    },
                    insertTexts: {
                        horizontalRule: ["", "\n\n-----\n\n"],
                        image: ["![](http://", ")"],
                        link: ["[", "](http://)"],
                        table: ["", "\n\n| Column 1 | Column 2 | Column 3 |\n| -------- | -------- | -------- |\n| Text     | Text      | Text     |\n\n"],
                    },
                    placeholder: "Type here...",
                    hideIcons: ["side-by-side", "fullscreen"],
                });
            </script>

            <script src="/assets/js/forms/dev/getStoreID.js"></script>
            <script src="/assets/js/forms/validator.js"></script>
            <script src="/assets/js/forms/dev/editStore.js"></script>
            <script src="/assets/js/forms/dev/buildStore.js"></script>

            <?php
}

include("views/dev/panel/template/main.php");

unset($GLOBALS['game']);
<?php

$title = "Términos y condiciones de Orion";

function showPage() {
    ?>

    <div class="p-5">
        <?php include("views/legal/texts/terms.php"); ?>
    </div>

    <?php
}

include("views/templates/main.php");
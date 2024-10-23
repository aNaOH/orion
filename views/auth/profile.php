<?php

$title = "Entrar a Orion";

function showPage() {

  $user = User::getById($_SESSION['user']['id']);

  if(!isset($user)){
    //lol
  }
    ?>

    <!-- Page Title -->
    <div class="page-title dark-background">
      <div class="container position-relative">
        
      </div>
    </div><!-- End Page Title -->

    <!-- Starter Section Section -->
    <section id="starter-section" class="starter-section section">

    <div class="container">
      <div class="row ">

        <div class="col-md-6">

        <div class="card profile-card" style="width: 18rem;">
          <img src="/media/profile/<?= $user->profile_pic ?? "default" ?>" class="card-img-top" alt="...">
          <div class="card-body">
            <h5 class="card-title"><?= $user->username ?></h5>
            <p class="card-text"><?= $user->motd ?? "Este usuario no tiene estado" ?></p>
            <a href="#" class="btn btn-primary">Editar perfil</a>
          </div>
        </div>
        
        </div>

        <div class="col-md-6">

        </div>

      </div>
    </div>

    </section><!-- /Starter Section Section -->

    <?php
}

include("views/templates/main.php");
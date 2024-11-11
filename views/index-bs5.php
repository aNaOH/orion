<?php

$title = "Bienvenido a Orion";

function showPage() {
    ?>

    <!-- Hero Section -->
    <section id="hero" class="hero section dark-background">

        <div class="carousel-container">
            <h2 class="animate__animated animate__fadeInDown">Bienvenido a <span>Orion</span></h2>
            <p class="animate__animated animate__fadeInUp">Gamers, ¡unidos!</p>
            <div class="d-flex flex-row justify-content-between">
                <a href="#about" class="btn-get-started animate__animated animate__fadeInUp scrollto">Leer más</a>
                <?php if(isset($userSession)) { ?>
                  <a href="/register" class="btn-get-started animate__animated animate__fadeInUp">Unirse</a>
                <?php } ?>
            </div>
          </div>

    </section><!-- /Hero Section -->

    <!-- About Section -->
    <section id="about" class="about section">

      <!-- Section Title -->
      <div class="container section-title" data-aos="fade-up">
        <h2>Sobre Orion</h2>
        <p>¿Qué es?</p>
      </div><!-- End Section Title -->

      <div class="container">

        <div class="row gy-4">

          <div class="col-lg-6 content" data-aos="fade-up" data-aos-delay="100">
            <p>
              Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore
              magna aliqua.
            </p>
            <ul>
              <li><i class="bi bi-check2-circle"></i> <span>Ullamco laboris nisi ut aliquip ex ea commodo consequat.</span></li>
              <li><i class="bi bi-check2-circle"></i> <span>Duis aute irure dolor in reprehenderit in voluptate velit.</span></li>
              <li><i class="bi bi-check2-circle"></i> <span>Ullamco laboris nisi ut aliquip ex ea commodo</span></li>
            </ul>
          </div>

          <div class="col-lg-6" data-aos="fade-up" data-aos-delay="200">
            <p>Ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum. </p>
            <a href="/register" class="read-more"><span>Unirse a Orion</span></a>
          </div>

        </div>

      </div>

    </section><!-- /About Section -->

    <!-- Features Section -->
    <section id="features" class="features section">

        <div class="container">

            <ul class="nav nav-tabs row  d-flex" data-aos="fade-up" data-aos-delay="100">
            <li class="nav-item col-3">
                <a class="nav-link active show" data-bs-toggle="tab" data-bs-target="#features-tab-1">
                <i class="bi bi-binoculars"></i>
                <h4 class="d-none d-lg-block">Modi sit est dela pireda nest</h4>
                </a>
            </li>
            <li class="nav-item col-3">
                <a class="nav-link" data-bs-toggle="tab" data-bs-target="#features-tab-2">
                <i class="bi bi-box-seam"></i>
                <h4 class="d-none d-lg-block">Unde praesenti mara setra le</h4>
                </a>
            </li>
            <li class="nav-item col-3">
                <a class="nav-link" data-bs-toggle="tab" data-bs-target="#features-tab-3">
                <i class="bi bi-brightness-high"></i>
                <h4 class="d-none d-lg-block">Pariatur explica nitro dela</h4>
                </a>
            </li>
            <li class="nav-item col-3">
                <a class="nav-link" data-bs-toggle="tab" data-bs-target="#features-tab-4">
                <i class="bi bi-command"></i>
                <h4 class="d-none d-lg-block">Nostrum qui dile node</h4>
                </a>
            </li>
            </ul><!-- End Tab Nav -->

        </div>

    </section><!-- /Features Section -->

    <?php
}

include("views/templates/main.php");
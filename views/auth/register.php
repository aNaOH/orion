<?php

$title = "Unirse a Orion";

function showPage() {
    ?>

    <!-- Page Title -->
    <div class="page-title dark-background">
      <div class="container position-relative">
        <h1>Starter Page</h1>
        <p>Esse dolorum voluptatum ullam est sint nemo et est ipsa porro placeat quibusdam quia assumenda numquam molestias.</p>
        <nav class="breadcrumbs">
          <ol>
            <li><a href="index.html">Home</a></li>
            <li class="current">Starter Page</li>
          </ol>
        </nav>
      </div>
    </div><!-- End Page Title -->

    <!-- Starter Section Section -->
    <section id="starter-section" class="starter-section section">

      <div class="container" data-aos="fade-up">
        <p>Use this page as a starter for your own custom pages.</p>
      </div>

    </section><!-- /Starter Section Section -->

    <script>
        let form = document.getElementById("registerForm");

        form.onsubmit = (e) => {
            e.preventDefault();
            console.log("lol");
        };
    </script>

    <?php
}

include("views/templates/main.php");
<?php

$title = "Entrar a Orion";

function showPage() {
    ?>

    <!-- Page Title -->
    <div class="page-title dark-background">
      <div class="container position-relative">
        <h1>Entrar a Orion</h1>
      </div>
    </div><!-- End Page Title -->

    <!-- Starter Section Section -->
    <section id="starter-section" class="starter-section section">

    <div class="container px-5 my-5">
      <?php

      if(isset($_GET['from']) && $_GET['from'] == "register"){
        ?>
          <div class="alert alert-success" role="alert">
            ¡Cuenta creada! Ya puedes entrar.
          </div>
        <?php
      }

      ?>
        <form id="registerForm" novalidate>
            <div class="form-floating mb-3">
                <input class="form-control" id="emailAddress" name="emailAddress" type="email" placeholder="Email Address" required />
                <label for="emailAddress">Correo electrónico</label>
                <div id="emailAddressError" class="invalid-feedback"></div>
            </div>
            <div class="form-floating mb-3">
                <input class="form-control" id="password" name="password" type="password" placeholder="Password" required />
                <label for="password">Contraseña</label>
                <div id="passwordError" class="invalid-feedback"></div>
            </div>
            <div class="d-grid">
                <button class="btn btn-primary btn-lg" id="submitButton" type="submit">Iniciar sesión</button>
            </div>
        </form>
    </div>

    </section><!-- /Starter Section Section -->

    <script src="/assets/js/forms/validator.js"></script>
    <script src="/assets/js/forms/login.js"></script>

    <?php
}

include("views/templates/main.php");
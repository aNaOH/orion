<?php

$title = "Unirse a Orion";

function showPage() {
    ?>

    <!-- Page Title -->
    <div class="page-title dark-background">
      <div class="container position-relative">
        <h1>Unirse a Orion</h1>
      </div>
    </div><!-- End Page Title -->

    <!-- Starter Section Section -->
    <section id="starter-section" class="starter-section section">

    <div class="container px-5 my-5">
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
            <div class="form-floating mb-3">
                <input class="form-control" id="confirmPassword" name="confirmPassword" type="password" placeholder="Confirm password" required />
                <label for="confirmPassword">Repetir contraseña</label>
                <div id="confirmPasswordError" class="invalid-feedback"></div>
            </div>
            <div class="mb-3">
                <input class="form-check-input" id="terms" name="terms" type="checkbox" value="yeah" required />
                <label class="form-check-label" for="terms">
                    Acepto los Términos y Condiciones y la Política de Privacidad.
                </label>
                <div id="termsError" class="invalid-feedback"></div>
            </div>
            <div class="d-grid">
                <button class="btn btn-primary btn-lg" id="submitButton" type="submit">Unirse</button>
            </div>
        </form>
    </div>


    </section><!-- /Starter Section Section -->

    <script src="/assets/js/forms/validator.js"></script>
    <script src="/assets/js/forms/register.js"></script>

    <?php
}

include("views/templates/main.php");
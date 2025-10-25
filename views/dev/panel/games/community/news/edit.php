<?php

$title =
    "Editar noticia " . htmlspecialchars($new->title) . " | Orion Dev Panel";

function showPage()
{
    global $game;
    global $new;
    global $newscategories;
    ?>
    <link rel="stylesheet" href="/assets/vendor/simplemde/simplemde.orion.css">
    <script src="/assets/vendor/simplemde/simplemde.min.js"></script>

    <div id="edit-new" class="block">
      <form id="editNewForm" class="space-y-6">
        <?php OrionComponents::TokenInput(ETOKEN_TYPE::DEVACTION, [
            "userID" => $_SESSION["user"]["id"],
            "gameID" => $game->id,
        ]); ?>
        <input type="hidden" name="game" value="<?= $game->id ?>">
        <input type="hidden" name="new" value="<?= $new->id ?>">

        <!-- Título -->
        <div class="relative">
          <input
            type="text"
            id="title"
            name="title"
            value="<?= htmlspecialchars($new->title) ?>"
            class="peer w-full bg-[#0f172a] border border-gray-600 text-gray-200 rounded-lg px-4 pt-6 pb-2
                   focus:ring-2 focus:ring-alt focus:border-alt outline-none placeholder-transparent"
            required
          />
          <label
            for="title"
            class="absolute left-4 top-3 text-gray-400 text-sm transition-all
                   peer-placeholder-shown:top-4 peer-placeholder-shown:text-base peer-placeholder-shown:text-gray-500
                   peer-focus:top-2.5 peer-focus:text-sm peer-focus:text-alt"
          >
            Título
          </label>
          <div class="invalid-feedback text-red-400 mt-1 text-sm" id="titleError"></div>
        </div>

        <!-- Categoría -->
        <div>
          <label for="category" class="block mb-2 text-gray-300 font-medium">Categoría</label>
          <select
            id="category"
            name="category"
            class="w-full bg-[#0f172a] border border-gray-600 text-gray-200 rounded-lg px-4 py-3
                   focus:ring-2 focus:ring-alt focus:border-alt outline-none"
            required
          >
            <?php foreach ($newscategories as $category): ?>
              <option
                value="<?= $category->id ?>"
                <?= $category->id == $new->getPostInfo()->category_id
                    ? "selected"
                    : "" ?>
              >
                <?= htmlspecialchars($category->name) ?>
              </option>
            <?php endforeach; ?>
          </select>
          <div class="invalid-feedback text-red-400 mt-1 text-sm" id="categoryError"></div>
        </div>

        <!-- Cuerpo -->
        <div class="relative">
          <label for="body" class="block mb-2 text-gray-300 font-medium">Cuerpo</label>
          <textarea
            id="body"
            name="body"
            class="w-full bg-[#0f172a] border border-gray-600 text-gray-200 rounded-lg px-4 pt-6 pb-2
                   focus:ring-2 focus:ring-alt focus:border-alt outline-none"
            rows="10"
            required
          ><?= htmlspecialchars($new->body) ?></textarea>
          <div class="invalid-feedback text-red-400 mt-1 text-sm" id="bodyError"></div>
        </div>

        <!-- Botón -->
        <div class="flex flex-col gap-3 mt-6">
          <button
            id="submitButton"
            type="submit"
            class="flex justify-center items-center gap-2 bg-alt text-[#1B2A49] font-medium px-6 py-3 rounded-lg hover:opacity-90 transition w-full"
          >
            <i class="bi bi-arrow-repeat hidden animate-spin" id="spinnerNews"></i>
            <span>Actualizar noticia</span>
          </button>
        </div>
      </form>
    </div>

    <script src="/assets/js/forms/validator.js"></script>
    <script src="/assets/js/orion-panel/markdown-editor.js"></script>
    <script>
      const simplemde = setupMarkdownEditor({
        selector: "#body",
        uniqueId: "OrionDev_Edit_GameNew_<?= $game->id ?>_<?= $new->id ?>"
      });

      const form = document.getElementById("editNewForm");
      const submitButton = document.getElementById("submitButton");
      const spinner = document.getElementById("spinnerNews");
      const bodyError = document.getElementById("bodyError");

      form.addEventListener("submit", async (event) => {
        event.preventDefault();

        spinner.classList.remove("hidden");
        submitButton.disabled = true;

        const formData = new FormData(form);
        const response = await fetch("/api/dev/news-edit", {
          method: "POST",
          body: formData
        });

        const data = await response.json();

        if (data.status === 201) {
          window.location.href = "/dev/panel/games/<?= $game->id ?>/community/news";
        } else {
          bodyError.textContent = data.error || "Ocurrió un error al actualizar la noticia.";
        }

        spinner.classList.add("hidden");
        submitButton.disabled = false;
      });
    </script>

    <?php
}

include "views/templates/panel/dev.php";

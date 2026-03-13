/* ---------------------------------------------------------
   CONFIGURACIÓN DE SIMPLEMDE
--------------------------------------------------------- */
const simplemde = new SimpleMDE({
  element: document.getElementById("description"),
  autosave: {
    enabled: true,
    uniqueId: "Orion_StoreGame_" + gameID.toString() + "_Description",
    delay: 1000,
  },
  insertTexts: {
    horizontalRule: ["", "\n\n-----\n\n"],
    link: ["[", "](http://)"],
    table: [
      "",
      "\n\n| Column 1 | Column 2 | Column 3 |\n| -------- | -------- | -------- |\n| Text     | Text      | Text     |\n\n",
    ],
  },
  placeholder: "Type here...",
  toolbar: [
    {
      name: "bold",
      action: SimpleMDE.toggleBold,
      className: "fa fa-bold !text-alt",
      title: "Bold",
    },
    {
      name: "italic",
      action: SimpleMDE.toggleItalic,
      className: "fa fa-italic !text-alt",
      title: "Italic",
    },
    {
      name: "underline",
      action: SimpleMDE.toggleUnderline,
      className: "fa fa-underline !text-alt",
      title: "Underline",
    },
    {
      name: "heading",
      action: SimpleMDE.toggleHeadingSmaller,
      className: "fa fa-header !text-alt",
      title: "Heading",
    },
    "|",
    {
      name: "link",
      action: SimpleMDE.toggleLink,
      className: "fa fa-link !text-alt",
      title: "Link",
    },
    {
      name: "table",
      action: SimpleMDE.toggleTable,
      className: "fa fa-table !text-alt",
      title: "Table",
    },
    "|",
    {
      name: "preview",
      action: SimpleMDE.togglePreview,
      className: "fa fa-eye !text-alt no-disable",
      title: "Preview",
    },
  ],
  previewRender: function (plainText) {
    const html = SimpleMDE.prototype.markdown(plainText);
    const temp = document.createElement("div");
    temp.innerHTML = html;

    const applyClass = (sel, cls) =>
      temp.querySelectorAll(sel).forEach((el) => (el.className = cls));

    applyClass("h1", "text-4xl font-bold my-4");
    applyClass("h2", "text-2xl font-bold my-4");
    applyClass("h3", "text-xl font-bold my-4");
    applyClass("h4", "text-lg font-bold my-4");
    applyClass("p", "text-base leading-relaxed my-2");
    applyClass("ul", "list-disc pl-5 my-2");
    applyClass("ol", "list-decimal pl-5 my-2");
    applyClass("a", "text-blue-500 hover:underline");
    applyClass("img", "max-w-full h-auto rounded");

    return temp.innerHTML;
  },
});

const features = [];
const featuresContainer = document.getElementById("featuresContainer");
const featureSelector = document.getElementById("featureSelector");
const addFeatureButton = document.getElementById("addFeatureButton");

async function fetchFeatures() {
  try {
    const response = await fetch("/api/dev/features");
    const responseJSON = await response.json();
    const data = responseJSON.data;
    features.push(...data);
  } catch (error) {
    console.error("Error fetching features:", error);
  }
}

// --- Reordenar los elementos visualmente ---
function reorderFeatures() {
  if (features.length === 0) return;

  const orderMap = new Map(features.map((f, i) => [f.id.toString(), i]));
  const elements = Array.from(featuresContainer.children);

  elements.sort((a, b) => {
    const aId = a.dataset.featureId;
    const bId = b.dataset.featureId;
    return (orderMap.get(aId) ?? 0) - (orderMap.get(bId) ?? 0);
  });

  featuresContainer.innerHTML = "";
  for (const el of elements) {
    featuresContainer.appendChild(el);
  }
}

// --- Agregar una feature visualmente ---
async function addFeature(featureId) {
  // Si aún no se han cargado las features, esperar a que se carguen
  if (features.length === 0) {
    await fetchFeatures();
  }

  const feature = features.find((f) => f.id.toString() === featureId);
  if (!feature) {
    console.warn(`Feature con id ${featureId} no encontrada.`);
    return;
  }

  // Evitar duplicados
  if (featuresContainer.querySelector(`[data-feature-id="${featureId}"]`)) {
    console.warn("Esa feature ya está agregada.");
    return;
  }

  const featureElement = document.createElement("div");
  featureElement.classList.add("flex", "items-center", "gap-2");
  featureElement.dataset.featureId = featureId;

  const gradientChip = document.createElement("gradient-chip");
  gradientChip.setAttribute("base-color", feature.tint || "#007bff");
  gradientChip.setAttribute("size", 24);
  gradientChip.setAttribute(
    "icon-path",
    `https://cdn.orion.moonnastd.com/game/feature/${feature.icon}`,
  );
  gradientChip.setAttribute("text", feature.name);
  gradientChip.setAttribute("border-radius", 8);
  gradientChip.classList.add("w-full");

  const deleteButton = document.createElement("button");
  deleteButton.type = "button";
  deleteButton.classList.add(
    "bg-red-500",
    "text-white",
    "rounded-full",
    "px-2",
    "py-1",
    "hover:bg-red-600",
  );
  deleteButton.dataset.featureId = featureId;
  deleteButton.textContent = "Eliminar";
  deleteButton.addEventListener("click", () => deleteFeature(featureId));

  featureElement.appendChild(gradientChip);
  featureElement.appendChild(deleteButton);

  featuresContainer.appendChild(featureElement);
  reorderFeatures();
}

// --- Eliminar una feature del contenedor ---
function deleteFeature(featureId) {
  const featureElement = document.querySelector(
    `[data-feature-id="${featureId}"]`,
  );
  if (featureElement) {
    featureElement.remove();
    reorderFeatures();
  }
}

// --- Listener para agregar features ---
addFeatureButton.addEventListener("click", async () => {
  const selectedFeature = featureSelector.value;
  if (selectedFeature) {
    await addFeature(selectedFeature);
  }
});

/* ---------------------------------------------------------
   FUNCIONES GENERALES
--------------------------------------------------------- */
const toggleSpinner = (button, show, text = "Procesando...") => {
  button.disabled = show;
  button.innerHTML = show
    ? `<div class="flex items-center justify-center gap-2">
         <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>${text}
       </div>`
    : button.dataset.defaultText || "Enviar";
};

const updateDeveloperField = () => {
  const asEditor = document.getElementById("asEditor");
  const devContainer = document.getElementById("developerNameContainer");
  devContainer.classList.toggle("hidden", !asEditor.checked);
};

/* ---------------------------------------------------------
   TABS
--------------------------------------------------------- */
document.querySelectorAll(".tab-button").forEach((btn) => {
  btn.addEventListener("click", () => {
    document
      .querySelectorAll(".tab-button")
      .forEach((b) => b.classList.replace("text-alt", "text-gray-400"));
    document
      .querySelectorAll(".tab-button")
      .forEach((b) => b.classList.replace("border-alt", "border-transparent"));

    document
      .querySelectorAll(".tab-pane")
      .forEach((tab) => tab.classList.add("hidden"));

    btn.classList.replace("text-gray-400", "text-alt");
    btn.classList.replace("border-transparent", "border-alt");

    document.getElementById(btn.dataset.tab).classList.remove("hidden");
  });
});

/* ---------------------------------------------------------
   FORMULARIO: EDITAR JUEGO
--------------------------------------------------------- */
const formEdit = document.getElementById("editGameForm");
const submitEdit = document.getElementById("submitButtonEdit");
const visibilityButton = document.getElementById("changeVisibility");

if (formEdit) {
  document
    .getElementById("asEditor")
    .addEventListener("change", updateDeveloperField);
  updateDeveloperField();

  formEdit.onsubmit = async (e) => {
    e.preventDefault();

    [
      "title",
      "shortDescription",
      "developerName",
      "description",
      "price",
      "discount",
    ].forEach((id) => resetField(id));

    // Crear objeto con los datos del formulario
    const data = {
      game: gameID,
      title: document.getElementById("title").value,
      shortDescription: document.getElementById("shortDescription").value,
      asEditor: document.getElementById("asEditor").checked,
      developerName: document.getElementById("developerName").value,
      description: simplemde.value(),
      price: document.getElementById("price").value,
      discount: document.getElementById("discount").value,
      genre: document.getElementById("genre").value,
      features: Array.from(
        featuresContainer.querySelectorAll("[data-feature-id]"),
      ).map((el) => el.dataset.featureId),
    };

    const formData = new FormData();
    formData.append("data", JSON.stringify(data));

    // Adjuntar archivos (si existen)
    ["coverFile", "thumbFile", "iconFile"].forEach((id) => {
      const input = document.getElementById(id).getFileInput?.();
      if (input && input[0]) formData.append(id, input[0]);
    });

    submitEdit.dataset.defaultText = "Cambiar";
    toggleSpinner(submitEdit, true, "Guardando...");

    $.ajax({
      url: "/api/dev/game/store",
      type: "POST",
      data: formData,
      processData: false,
      contentType: false,
      success: () => (location.href = `/dev/panel/games/${gameID}/store`),
      error: (xhr) => {
        //showError(xhr.responseJSON);
        toggleSpinner(submitEdit, false);
      },
    });
  };
}

/* ---------------------------------------------------------
   FORMULARIO: SUBIR BUILD (chunked upload)
--------------------------------------------------------- */
const CHUNK_SIZE = 7 * 1024 * 1024; // 7 MB — bajo el límite de 8 MB de PHP

const formBuild = document.getElementById("buildForm");
const submitBuild = document.getElementById("submitButtonBuild");

if (formBuild) {
  formBuild.onsubmit = async (e) => {
    e.preventDefault();
    resetField("version");

    const fields = e.target.elements;
    const file = document.getElementById("file").files[0];
    const version = fields["version"].value;

    if (!file)
      return showError({
        field: "file",
        message: "Selecciona un archivo .zip",
      });
    if (!version)
      return showError({ field: "version", message: "Indica una versión" });

    submitBuild.dataset.defaultText = "Subir";
    toggleSpinner(submitBuild, true, "Preparando...");

    // ── Barra de progreso ──────────────────────────────────────────────
    let progressContainer = document.getElementById("uploadProgress");
    if (!progressContainer) {
      progressContainer = document.createElement("div");
      progressContainer.id = "uploadProgress";
      progressContainer.className =
        "w-full bg-gray-700 rounded-lg overflow-hidden mt-3";
      progressContainer.innerHTML = `
        <div id="uploadProgressBar"
             class="bg-alt text-[#1B2A49] text-xs font-medium text-center py-1 transition-all duration-200"
             style="width:0%">0%</div>
        <p id="uploadProgressLabel" class="text-xs text-center text-gray-400 mt-1"></p>`;
      formBuild.appendChild(progressContainer);
    }

    const bar = document.getElementById("uploadProgressBar");
    const label = document.getElementById("uploadProgressLabel");

    const setProgress = (loaded, total, status = "") => {
      const pct = Math.round((loaded / total) * 100);
      bar.style.width = pct + "%";
      bar.innerText = pct + "%";
      label.innerText = status;
    };

    // ── Chunking ───────────────────────────────────────────────────────
    const totalChunks = Math.ceil(file.size / CHUNK_SIZE);
    const uploadId = crypto.randomUUID(); // ID único para esta subida

    try {
      for (let index = 0; index < totalChunks; index++) {
        const start = index * CHUNK_SIZE;
        const end = Math.min(start + CHUNK_SIZE, file.size);
        const chunk = file.slice(start, end);
        const isLast = index === totalChunks - 1;

        const formData = new FormData();
        formData.append("game", gameID);
        formData.append("version", version);
        formData.append("upload_id", uploadId);
        formData.append("chunk_index", index);
        formData.append("total_chunks", totalChunks);
        formData.append("filename", file.name);
        formData.append("file", chunk, file.name);

        toggleSpinner(
          submitBuild,
          true,
          `Subiendo ${index + 1}/${totalChunks}...`,
        );

        await uploadChunk(formData, (chunkLoaded, chunkTotal) => {
          const globalLoaded =
            start + (chunkLoaded / chunkTotal) * (end - start);
          setProgress(
            globalLoaded,
            file.size,
            `Parte ${index + 1} de ${totalChunks}`,
          );
        });

        // Si es el último chunk el servidor devolverá la respuesta final
        if (isLast) {
          setProgress(file.size, file.size, "¡Subida completada!");
        }
      }

      // Éxito
      toggleSpinner(submitBuild, false);
      setTimeout(() => {
        progressContainer.remove();
        location.reload();
      }, 1500);
    } catch (err) {
      showError({
        field: "file",
        message: err.message || "Error al subir el archivo",
      });
      toggleSpinner(submitBuild, false);
      setProgress(0, 1, "");
    }
  };
}

/**
 * Sube un chunk y reporta progreso.
 * Resuelve con la respuesta JSON del servidor en el último chunk.
 * Rechaza con un Error si el servidor devuelve un error.
 */
function uploadChunk(formData, onProgress) {
  return new Promise((resolve, reject) => {
    $.ajax({
      xhr: () => {
        const xhr = new window.XMLHttpRequest();
        xhr.upload.addEventListener("progress", (e) => {
          if (e.lengthComputable) onProgress(e.loaded, e.total);
        });
        return xhr;
      },
      url: "/api/dev/game/build/chunk",
      type: "POST",
      data: formData,
      processData: false,
      contentType: false,
      success: (res) => resolve(res),
      error: (xhr) => {
        const msg =
          xhr.responseJSON?.message || `Error en chunk (HTTP ${xhr.status})`;
        reject(new Error(msg));
      },
    });
  });
}

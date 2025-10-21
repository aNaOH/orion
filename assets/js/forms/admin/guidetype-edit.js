let form = document.getElementById("guideTypeForm");
let submit = document.getElementById("submitButton");

form.onsubmit = (e) => {
  e.preventDefault();

  let fields = e.target.elements;

  submit.classList.add("disabled");

  // Crear un objeto FormData
  let formData = new FormData();
  formData.append("type", fields["type"].value); // Agregar el título
  formData.append("tintColor", fields["tintColor"].value); // Agregar el color

  // Realizar la solicitud AJAX con FormData
  $.ajax({
    url: "/api/admin/guidetype/edit", // La URL del endpoint
    type: "POST", // Método HTTP
    data: formData, // FormData con los datos del formulario
    processData: false, // Evitar que jQuery procese los datos
    contentType: false, // Evitar que jQuery establezca el encabezado Content-Type
    success: function (response) {
      location.href = "/admin/guidetypes";
    },
    error: function (xhr, status, error) {
      const info = xhr.responseJSON;
      console.log(info);
      //showError(info);
      submit.classList.remove("disabled");
    },
  });
};

//Previsualización
const svgUploadInput = document.getElementById("icon");
const tintColorInput = document.getElementById("tintColor");
const svgPreview = document.getElementById("preview");
const previewContainer = document.getElementById("previewContainer");

// Inicialmente deshabilitar el color hasta que haya un SVG
tintColorInput.disabled = true;

// Manejar cambios en el color
tintColorInput.addEventListener("input", (event) => {
  const color = event.target.value;
  svgPreview.setAttribute("base-color", color);
});

// Manejar la subida de archivos SVG
svgUploadInput.addEventListener("change", async (event) => {
  const file = event.target.files[0];

  // Si no hay archivo, ocultar la previsualización
  if (!file) {
    previewContainer.classList.add("hidden");
    tintColorInput.disabled = true;
    return;
  }

  const reader = new FileReader();
  reader.onload = () => {
    const svgContent = reader.result;
    const parser = new DOMParser();
    const doc = parser.parseFromString(svgContent, "image/svg+xml");
    const svg = doc.querySelector("svg");

    if (svg) {
      // Mostrar la previsualización y habilitar el color
      previewContainer.classList.remove("hidden");
      tintColorInput.disabled = false;

      // Reemplaza el contenido del componente con el SVG cargado
      svgPreview.innerHTML = "";
      if (svgPreview.svgContainer) {
        svgPreview.svgContainer.innerHTML =
          new XMLSerializer().serializeToString(svg);
      } else {
        // Compatibilidad por si no existe svgContainer internamente
        svgPreview.innerHTML = new XMLSerializer().serializeToString(svg);
      }

      // Aplica el color actual
      svgPreview.setAttribute("base-color", tintColorInput.value);
    } else {
      console.error("El archivo cargado no contiene un SVG válido.");
      previewContainer.classList.add("hidden");
      tintColorInput.disabled = true;
    }
  };

  reader.onerror = () => {
    console.error("Error leyendo el archivo SVG.");
    previewContainer.classList.add("hidden");
    tintColorInput.disabled = true;
  };

  reader.readAsText(file);
});

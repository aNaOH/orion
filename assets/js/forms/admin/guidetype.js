let form = document.getElementById("guideTypeForm");
let submit = document.getElementById("submitButton");

form.onsubmit = (e) => {
    e.preventDefault();

    let fields = e.target.elements;

    submit.classList.add("disabled");

    // Crear un objeto FormData
    let formData = new FormData();
    formData.append("type", fields["type"].value); // Agregar el título
    formData.append("icon", fields["icon"].files[0]); // Agregar la imagen
    formData.append("tintColor", fields["tintColor"].value); // Agregar el color

    // Realizar la solicitud AJAX con FormData
    $.ajax({
        url: '/api/admin/guidetype', // La URL del endpoint
        type: 'POST', // Método HTTP
        data: formData, // FormData con los datos del formulario
        processData: false, // Evitar que jQuery procese los datos
        contentType: false, // Evitar que jQuery establezca el encabezado Content-Type
        success: function(response) {
            location.href = "/communities";
        },
        error: function(xhr, status, error) {
            const info = xhr.responseJSON;
            console.log(info);
            //showError(info);
            submit.classList.remove("disabled");
        }
    });
};

// Previsualización
const svgUploadInput = document.getElementById('icon');
const tintColorInput = document.getElementById('tintColor');
const svgPreview = document.getElementById('preview');
const previewLabel = document.getElementById('previewLabel');

  // Manejar cambios en el color
  tintColorInput.addEventListener('input', (event) => {
    const color = event.target.value;
    svgPreview.setAttribute('base-color', color);
  });

  // Manejar la subida de archivos SVG
  svgUploadInput.addEventListener('change', async (event) => {
    const file = event.target.files[0];

    // Si no hay archivo, ocultar la previsualización
    if (!file) {
      svgPreview.style.display = 'none';
      previewLabel.style.display = 'none';
      tintColorInput.disabled = true;
      return;
    }

    const reader = new FileReader();
    reader.onload = () => {
      const svgContent = reader.result;
      const parser = new DOMParser();
      const doc = parser.parseFromString(svgContent, 'image/svg+xml');
      const svg = doc.querySelector('svg');

      if (svg) {
        // Mostrar la previsualización y habilitar color
        svgPreview.style.display = 'block';
        previewLabel.style.display = 'block';
        tintColorInput.disabled = false;

        // Inserta el SVG como contenido del componente
        svgPreview.innerHTML = '';
        svgPreview.svgContainer.innerHTML = new XMLSerializer().serializeToString(svg);
      } else {
        console.error('El archivo cargado no contiene un SVG válido.');
      }
    };

    reader.onerror = () => {
      console.error('Error leyendo el archivo SVG.');
    };

    reader.readAsText(file);
  });
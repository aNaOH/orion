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
const tintColorInput = document.getElementById("tintColor");
const svgPreview = document.getElementById("preview");

// Manejar cambios en el color
tintColorInput.addEventListener("input", (event) => {
  const color = event.target.value;
  svgPreview.setAttribute("base-color", color);
});

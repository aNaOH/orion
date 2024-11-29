let form = document.getElementById("profileEditForm");
let submit = document.getElementById("submitButton");
let returnBtn = document.getElementById("returnBtn");
let profilePic = document.getElementById("profilePicImg");

document.getElementById('editPic').addEventListener('click', () => {
    document.getElementById('profilePic').click();
});
  
document.getElementById('profilePic').addEventListener('change', (event) => {
    const file = event.target.files[0];
  
    if (file && file.type.startsWith('image/')) {
      const reader = new FileReader();
      const img = new Image();
  
      // Leer el archivo como una URL de datos
      reader.onload = function (e) {
        img.src = e.target.result;
  
        img.onload = function () {
          // Verificar si la imagen es cuadrada
          if (img.width === img.height) {
            document.getElementById('profilePicImg').src = img.src;
            console.log('Imagen actualizada correctamente.');
          } else {
            alert('Por favor, selecciona una imagen cuadrada.');
          }
        };
      };
  
      reader.readAsDataURL(file);
    } else {
      alert('Por favor, selecciona un archivo de imagen válido.');
    }
});

form.onsubmit = (e) => {
    e.preventDefault();

    let fields = e.target.elements;

    submit.classList.add("disabled");
    returnBtn.classList.add("disabled");

    let formData = new FormData();
    formData.append("profilePic", fields["profilePic"].files[0]);
    formData.append("username", fields["username"].value);
    formData.append("motd", fields["motd"].value);
    formData.append("email", fields["email"].value);
    formData.append("currentPassword", fields["currentPassword"].value);
    formData.append("password", fields["password"].value);
    formData.append("confirmPassword", fields["confirmPassword"].value);
    formData.append("tript_token", fields["tript_token"].value);

    $.ajax({
        url: '/api/auth/edit', // The URL to which the request is sent
        type: 'POST', // The HTTP method to use for the request (GET, POST, etc.)
        data: formData, // FormData con los datos del formulario
        processData: false, // Evitar que jQuery procese los datos
        contentType: false, // Evitar que jQuery establezca el encabezado Content-Type
        success: function(response) {
            location.href = "/profile?from=edit";
        },
        error: function(xhr, status, error) {
            const info = xhr.responseJSON;
            console.log(info);
            submit.classList.remove("disabled");
            returnBtn.classList.remove("disabled");
        }
    });
};
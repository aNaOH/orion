let urlSplitted = document.URL.split('/');
let type = urlSplitted[urlSplitted.length-2];
let game = urlSplitted[urlSplitted.length-3];

let form = document.getElementById("communityCreateForm");
let submit = document.getElementById("submitButton");

form.onsubmit = (e) => {
    e.preventDefault();

    let fields = e.target.elements;

    let formData = new FormData();
    formData.append("title", fields["title"].value); // Agregar el título
    formData.append("body", document.getElementById("body").getFileInput()[0]); // Agregar la imagen
    formData.append("token", fields["tript_token"].value);

    submit.classList.add("disabled");

    $.ajax({
        url: '/api/communities/' + game.toString() + '/' + type, // The URL to which the request is sent
        type: 'POST', // The HTTP method to use for the request (GET, POST, etc.)
        data: formData, // Data to be sent to the server
        processData: false, // Evitar que jQuery procese los datos
        contentType: false, // Evitar que jQuery establezca el encabezado Content-Type
        success: function(response) {
            location.href = "/communities/" + game.toString() + "/" + type;
        },
        error: function(xhr, status, error) {
            const info = xhr.responseJSON;
            console.log(info);
            submit.classList.remove("disabled");
        }
    });
};
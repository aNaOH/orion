let formBuild = document.getElementById("buildForm");
let submitBuild = document.getElementById("submitButtonBuild");

formBuild.onsubmit = (e) => {
    e.preventDefault();

    let fields = e.target.elements;

    resetField('version');

    let formData = new FormData();
    formData.append("game", gameID);
    formData.append("version", fields["version"].value);
    formData.append("file", document.getElementById("file").files[0]);

    submitEdit.classList.add("disabled");
    submitEdit.innerHTML = "Subiendo...";

    $.ajax({
        url: '/api/dev/game/build', // The URL to which the request is sent
        type: 'POST', // The HTTP method to use for the request (GET, POST, etc.)
        data: formData, 
        processData: false, // Evitar que jQuery procese los datos
        contentType: false, // Evitar que jQuery establezca el encabezado Content-Type
        success: function(response) {
            location.href = "/dev/panel/games/" + gameID + "/store";
        },
        error: function(xhr, status, error) {
            const info = xhr.responseJSON;
            console.log(info);
            showError(info);
            submitEdit.classList.remove("disabled");
            submitEdit.innerHTML = "Subir";
        }
    });
};
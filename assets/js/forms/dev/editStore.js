let formEdit = document.getElementById("editGameForm");
let asEditor = document.getElementById("asEditor");
let developerNameContainer = document.getElementById("developerNameContainer");
let submitEdit = document.getElementById("submitButtonEdit");

let visibilityButton = document.getElementById("changeVisibility");

visibilityButton.onclick = (e) => {

    e.preventDefault();

    const newStatus = visibilityButton.dataset['status'] == 'public' ? 'hidden' : 'public';
    const isPublic = newStatus == 'public';

    visibilityButton.classList.add("disabled");

    $.ajax({
        url: '/api/dev/game/public', // The URL to which the request is sent
        type: 'POST', // The HTTP method to use for the request (GET, POST, etc.)
        data: {
            game: gameID,
            isPublic
        }, 
        success: function(response) {
            visibilityButton.dataset['status'] = newStatus;
            visibilityButton.innerHTML = newStatus == 'public' ? 'Ocultar' : 'Publicar';
            visibilityButton.classList.remove("disabled");
        },
        error: function(xhr, status, error) {
            const info = xhr.responseJSON;
            console.log(info);
            showError(info);
            visibilityButton.classList.remove("disabled");
        }
    });
}

if(asEditor.checked) {
    developerNameContainer.classList.remove("d-none");
} else {
    developerNameContainer.classList.add("d-none");
}

asEditor.onchange = (e) => {
    if(asEditor.checked) {
        developerNameContainer.classList.remove("d-none");
    } else {
        developerNameContainer.classList.add("d-none");
    }
}

formEdit.onsubmit = (e) => {
    e.preventDefault();

    let fields = e.target.elements;

    resetField('title');
    resetField('shortDescription');
    resetField('developerName');
    resetField('description');
    resetField('price');
    resetField('discount');

    let formData = new FormData();
    formData.append("game", gameID);
    formData.append("title", fields["title"].value);
    formData.append("asEditor", fields["asEditor"].checked);
    formData.append("shortDescription", fields["shortDescription"].value);
    formData.append("developerName", fields["developerName"].value);
    formData.append("description", fields["description"].value);
    formData.append("price", fields["price"].value);
    formData.append("discount", fields["discount"].value);
    
    if(document.getElementById("coverFile").getFileInput()){
        formData.append("coverFile", document.getElementById("coverFile").getFileInput()[0]);
    }

    if(document.getElementById("thumbFile").getFileInput()){
        formData.append("thumbFile", document.getElementById("thumbFile").getFileInput()[0]);
    }

    submitEdit.classList.add("disabled");

    $.ajax({
        url: '/api/dev/game/store', // The URL to which the request is sent
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
        }
    });
};
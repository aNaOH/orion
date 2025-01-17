let form = document.getElementById("newAchievementForm");
let typeSelector = document.getElementById("type");
let statContainer = document.getElementById("statContainer");
let submit = document.getElementById("submitButton");
let iconUpload = document.getElementById("icon");
let lockedIconUpload = document.getElementById("lockedIcon");

typeSelector.onchange = (e) => {
    if(typeSelector.value == 1) {
        statContainer.classList.remove("d-none");
    } else {
        statContainer.classList.add("d-none");
    }
}

form.onsubmit = (e) => {
    e.preventDefault();

    let fields = e.target.elements;

    resetField('name');
    resetField('description');

    submit.classList.add("disabled");

    let formData = new FormData();
    formData.append('token', fields['tript_token'].value);
    formData.append('game', fields['game'].value);
    formData.append('name', fields['name'].value);
    formData.append('description', fields['description'].value);
    formData.append('type', fields['type'].value);
    if (fields['type'].value == 1) {
        formData.append('stat', fields['stat'].value);
    }
    if (!iconUpload.getFileInput()) {
        showError({field: 'icon', message: "Icon is required."});
        submit.classList.remove("disabled");
        return;
    }
    formData.append('icon', iconUpload.getFileInput()[0]);
    if (lockedIconUpload.getFileInput()) {
        formData.append('lockedIcon', lockedIconUpload.getFileInput()[0]);
    }

    $.ajax({
        url: '/api/dev/achievement', // The URL to which the request is sent
        type: 'POST', // The HTTP method to use for the request (GET, POST, etc.)
        data: formData, // Data to be sent to the server
        processData: false,
        contentType: false,
        success: function(response) {
            location.href = "/dev/panel/games/" + gameID + "/community/achievements";
        },
        error: function(xhr, status, error) {
            const info = xhr.responseJSON;
            console.log(info);
            showError(info);
            submit.classList.remove("disabled");
        }
    });
};

let form = document.getElementById("newGameForm");
let asEditor = document.getElementById("asEditor");
let developerNameContainer = document.getElementById("developerNameContainer");
let submit = document.getElementById("submitButton");

asEditor.onchange = (e) => {
    if(asEditor.checked) {
        developerNameContainer.classList.remove("d-none");
    } else {
        developerNameContainer.classList.add("d-none");
    }
}

form.onsubmit = (e) => {
    e.preventDefault();

    let fields = e.target.elements;

    resetField('title');
    resetField('shortDescription');
    resetField('developerName');

    submit.classList.add("disabled");

    $.ajax({
        url: '/api/dev/game', // The URL to which the request is sent
        type: 'POST', // The HTTP method to use for the request (GET, POST, etc.)
        data: { title: fields['title'].value, shortDescription: fields['shortDescription'].value, asEditor: asEditor.checked, developerName: fields['developerName'].value  }, // Data to be sent to the server
        success: function(response) {
            location.href = "/dev/panel/games";
        },
        error: function(xhr, status, error) {
            const info = xhr.responseJSON;
            console.log(info);
            showError(info);
            submit.classList.remove("disabled");
        }
    });
};
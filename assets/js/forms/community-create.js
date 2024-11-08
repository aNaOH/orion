let urlSplitted = document.URL.split('/');
let type = urlSplitted[urlSplitted.length-2];
let game = urlSplitted[urlSplitted.length-3];

let form = document.getElementById("communityCreateForm");
let submit = document.getElementById("submitButton");

form.onsubmit = (e) => {
    e.preventDefault();

    let fields = e.target.elements;

    submit.classList.add("disabled");

    $.ajax({
        url: '/api/communities/' + game.toString() + '/' + type, // The URL to which the request is sent
        type: 'POST', // The HTTP method to use for the request (GET, POST, etc.)
        data: { title: fields['title'].value, body: fields['body'].value, token: fields['tript_token'].value }, // Data to be sent to the server
        success: function(response) {
            location.href = "/";
        },
        error: function(xhr, status, error) {
            const info = xhr.responseJSON;
            console.log(info);
            submit.classList.remove("disabled");
        }
    });
};
let form = document.getElementById("quickGameForm");
let submit = document.getElementById("submitButton");

form.onsubmit = (e) => {
    e.preventDefault();

    let fields = e.target.elements;

    //resetField('title');

    submit.classList.add("disabled");

    $.ajax({
        url: '/api/admin/quickgame', // The URL to which the request is sent
        type: 'POST', // The HTTP method to use for the request (GET, POST, etc.)
        data: { title: fields['title'].value }, // Data to be sent to the server
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
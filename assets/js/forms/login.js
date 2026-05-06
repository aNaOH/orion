let form = document.getElementById("loginForm");
let submit = document.getElementById("submitButton");

form.onsubmit = (e) => {
    e.preventDefault();

    let fields = e.target.elements;

    resetField('email');
    resetField('password');

    submit.classList.add("disabled");

    $.ajax({
        url: '/api/auth/login', // The URL to which the request is sent
        type: 'POST', // The HTTP method to use for the request (GET, POST, etc.)
        data: { email: fields['email'].value, password: fields['password'].value, tript_token: fields['tript_token'].value }, // Data to be sent to the server
        success: function (response) {
            location.href = "/";
        },
        error: function (xhr, status, error) {
            const info = xhr.responseJSON;
            if (xhr.status === 403 && info.field === 'account') {
                location.href = "/suspended";
                return;
            }
            console.log(info);
            showError(info);
            submit.classList.remove("disabled");
        }
    });
};
let form = document.getElementById("registerForm");
let submit = document.getElementById("submitButton");

form.onsubmit = (e) => {
    e.preventDefault();

    let fields = e.target.elements;

    resetField('emailAddress');
    resetField('password');
    resetField('confirmPassword');
    resetField('birthdate');
    resetField('terms');

    $.ajax({
        url: '/api/auth/register', // The URL to which the request is sent
        type: 'POST', // The HTTP method to use for the request (GET, POST, etc.)
        data: { email: fields['emailAddress'].value, password: fields['password'].value, confirmPassword: fields['confirmPassword'].value, birthdate: fields['birthdate'].value, terms: fields['terms'].value, tript_token: fields['tript_token'].value }, // Data to be sent to the server
        success: function(response) {
            console.log("done");
            location.href = "/login?from=register";
        },
        error: function(xhr, status, error) {
            const info = xhr.responseJSON;
            console.log(info);
            showError(info);
            submit.classList.remove("disabled");
        }
    });
};
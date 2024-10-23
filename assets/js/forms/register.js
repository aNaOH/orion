let form = document.getElementById("registerForm");

form.onsubmit = (e) => {
    e.preventDefault();

    let fields = e.target.elements;

    if(fields['password'].value != fields['confirmPassword'].value) {
        console.log('wtf');
        return;
    }

    $.ajax({
        url: '/api/auth/register', // The URL to which the request is sent
        type: 'POST', // The HTTP method to use for the request (GET, POST, etc.)
        data: { email: fields['emailAddress'].value, password: fields['password'].value, confirmPassword: fields['confirmPassword'].value }, // Data to be sent to the server
        success: function(response) {
            location.href = "/login";
        },
        error: function(xhr, status, error) {
            // Code to execute if the request fails
            console.log('Error:', error);
        }
    });
};
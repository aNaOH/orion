let form = document.getElementById("registerForm");

form.onsubmit = (e) => {
    e.preventDefault();

    let fields = e.target.elements;

    $.ajax({
        url: '/api/auth/login', // The URL to which the request is sent
        type: 'POST', // The HTTP method to use for the request (GET, POST, etc.)
        data: { email: fields['emailAddress'].value, password: fields['password'].value }, // Data to be sent to the server
        success: function(response) {
            location.href = "/";
        },
        error: function(xhr, status, error) {
            // Code to execute if the request fails
            console.log('Error:', error);
        }
    });
};
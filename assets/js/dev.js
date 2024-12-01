$(document).ready(function () {
    // Realizar llamada AJAX a /api/home
    $.ajax({
        url: "/api/dev",
        method: "GET",
        success: function (response) {
            const developers = response.developers;

            document.getElementById("userCount").innerHTML = developers;
        },
        error: function (error) {
            console.error("Error al obtener los juegos:", error);
        }
    });

});

let moveToWhy = document.getElementById("moveToWhy");

moveToWhy.addEventListener('click', function(e) {
    e.preventDefault(); // Evitar el comportamiento por defecto del enlace

    // Seleccionar el destino
    const targetId = this.getAttribute('href').substring(1); // Obtiene el valor de la ID sin el '#'
    const targetElement = document.getElementById(targetId);

    // Realizar el desplazamiento animado
    window.scrollTo({
        top: targetElement.offsetTop, // Desplazarse hasta la posición del elemento
        behavior: 'smooth' // Hacerlo de forma suave
    });
});
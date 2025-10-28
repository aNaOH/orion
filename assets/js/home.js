$(document).ready(function () {
  // Realizar llamada AJAX a /api/home
  $.ajax({
    url: "/api/home",
    method: "GET",
    success: function (response) {
      const showcaseGames = response.showcaseGames;
      const users = response.users;

      document.getElementById("userCount").innerHTML = users;

      if (!showcaseGames || showcaseGames.length === 0) {
        console.error("No se encontraron juegos en la respuesta.");
        return;
      }

      // Seleccionar contenedores aleatoriamente y asignar juegos
      const containers = $("#hero .showcase-item");
      const selectedContainers = containers
        .toArray()
        .sort(() => Math.random() - 0.5)
        .slice(0, showcaseGames.length);

      showcaseGames.forEach((game, index) => {
        const container = $(selectedContainers[index]);
        const gameHtml = `
                    <img src="/media/game/thumb/${game.id}" alt="${game.title}"
                         class="w-full h-full object-cover rounded-md shadow-lg opacity-70 hover:opacity-100 transition duration-300 animate-float">
                `;
        container.html(gameHtml).hide().fadeIn(1000); // Efecto fade-in
      });
    },
    error: function (error) {
      console.error("Error al obtener los juegos:", error);
    },
  });
});

let form = document.getElementById("searchForm");

form.onsubmit = (e) => {
  e.preventDefault();

  let fields = e.target.elements;

  // Obtener el valor del campo de búsqueda
  const searchQuery = fields["search"].value.trim();

  // Obtener la opción seleccionada del selector
  const where = fields["where"].value;

  // Redirigir a la página con el parámetro de búsqueda
  window.location.href = `${where}/games?search=${encodeURIComponent(searchQuery)}`;
};

let moveToWhy = document.getElementById("moveToWhy");

moveToWhy.addEventListener("click", function (e) {
  e.preventDefault(); // Evitar el comportamiento por defecto del enlace

  // Seleccionar el destino
  const targetId = this.getAttribute("href").substring(1); // Obtiene el valor de la ID sin el '#'
  const targetElement = document.getElementById(targetId);

  // Realizar el desplazamiento animado
  window.scrollTo({
    top: targetElement.offsetTop, // Desplazarse hasta la posición del elemento
    behavior: "smooth", // Hacerlo de forma suave
  });
});

$(document).ready(function () {
  $.ajax({
    url: "/api/home",
    method: "GET",
    success: function (response) {
      const showcaseGames = response.showcaseGames;
      const users = response.users;

      $("#userCount").text(users);

      if (!showcaseGames || showcaseGames.length === 0) {
        console.error("No se encontraron juegos.");
        return;
      }

      const carousel = $("#showcaseCarousel");
      carousel.empty();

      // Duplicamos los juegos para efecto loop
      const gamesLoop = [...showcaseGames, ...showcaseGames];

      gamesLoop.forEach((game) => {
        carousel.append(`
                    <div class="w-[213px] flex-shrink-0">
                        <img src="/media/game/thumb/${game.id}"
                             alt="${game.title}"
                             class="w-full h-full object-cover rounded-lg shadow-md hover:opacity-90 transition duration-300">
                    </div>
                `);
      });

      // Calcular ancho total
      let totalWidth = (213 + 24) * showcaseGames.length;

      // Asignar variable CSS
      carousel[0].style.setProperty("--carousel-width", totalWidth + "px");

      // Ajustar duración de la animación según ancho (opcional)
      const speed = 50; // px por segundo
      const duration = firstSetWidth / speed;
      carousel.css("animation-duration", duration + "s");
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

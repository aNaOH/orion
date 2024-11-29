$(document).ready(function () {
    // Realizar llamada AJAX a /api/home
    $.ajax({
        url: "/api/home",
        method: "GET",
        success: function (response) {
            const showcaseGames = response.showcaseGames;

            if (!showcaseGames || showcaseGames.length === 0) {
                console.error("No se encontraron juegos en la respuesta.");
                return;
            }

            const containers = $("#hero .showcase-item");
            const selectedContainers = containers.toArray().sort(() => Math.random() - 0.5).slice(0, showcaseGames.length);

            showcaseGames.forEach((game, index) => {
                const container = $(selectedContainers[index]);
                const gameHtml = `
                    <img src="/media/game/thumb/${game.id}" alt="${game.title}" class="w-full h-full object-cover rounded-md shadow-lg animate-float">
                `;
                container.html(gameHtml).hide().fadeIn(1000); // Efecto fade-in
            });
        },
        error: function (error) {
            console.error("Error al obtener los juegos:", error);
        }
    });
});

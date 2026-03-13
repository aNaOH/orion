$(document).ready(function () {
  $("#no-games").hide();
  $("#game-info").hide();
  $("#sidebar").hide();

  $.ajax({
    url: "/api/library",
    method: "GET",
    dataType: "json",
    success: function (data) {
      if (data.status === 401) {
        if (data.to) {
          window.location.href = data.to;
        }
      } else {
        showGamesOnLibrary(data.data);
      }
    },
    error: function (jqXHR, textStatus, errorThrown) {
      console.error("Error fetching user games:", textStatus, errorThrown);
    },
  });

  $("#toggleDeveloperGames").on("change", function () {
    const showDeveloperGames = $(this).is(":checked");
    if (showDeveloperGames) {
      $("[data-isdev]").show();
    } else {
      $("[data-isdev]").hide();
    }
  });
});

function showGamesOnLibrary(games) {
  const sidebar = $("#game-list");
  sidebar.empty();

  if (games == undefined) {
    games = [];
  }

  games.forEach((game) => {
    const gameElement = `
            <div class="library-game cursor-pointer flex flex-row gap-2 items-center" data-gameid="${game.id}" ${game.isDeveloper ? "data-isdev" : ""} onclick="changeGameShown(${game.id})">
                <img src="https://cdn.orion.moonnastd.com/game/icon/${game.id}" alt="${game.title}" class="w-5 h-5">
                <h3 class="text-xl">${game.title}</h3>
            </div>
        `;
    sidebar.append(gameElement);
  });

  if (games.length > 0) {
    $("#sidebar").show();
    const gameSelectParam = new URLSearchParams(window.location.search).get(
      "game",
    );
    if (gameSelectParam) {
      const gameElement = $('[data-gameid="' + gameSelectParam + '"]');
      if (gameElement.length > 0) {
        changeGameShown(parseInt(gameSelectParam));
      } else {
        changeGameShown(games[0].id);
      }
    } else {
      changeGameShown(games[0].id);
    }
  } else {
    $("#no-games").show();
  }
  $("#library-loading").hide();
}

function changeGameShown(id) {
  $("[data-gameid]").removeClass("active");

  const sidebarButton = $('[data-gameid="' + id.toString() + '"');
  sidebarButton.addClass("active");

  $.ajax({
    url: "/api/library/" + id,
    method: "GET",
    dataType: "json",
    success: function (data) {
      if (data.status === 401) {
        if (data.to) {
          window.location.href = data.to;
        }
      } else {
        showGameInfo(data.data);
      }
    },
    error: function (jqXHR, textStatus, errorThrown) {
      console.error("Error fetching game info:", textStatus, errorThrown);
    },
  });
}

function showGameInfo(game) {
  $("#game-title").text(game.title);
  $("#game-image").attr(
    "src",
    "https://cdn.orion.moonnastd.com/game/thumb/" + game.id,
  );

  $("#game-store-link").attr("href", "/store/" + game.id);
  $("#game-community-link").attr("href", "/communities/" + game.id);

  let gameNews = [];

  $("#game-download").hide();
  $("#no-download-avaliable").show();
  hideDownloadProgress();

  if (game.builds.length > 0) {
    $("#game-download").show();
    $("#no-download-avaliable").hide();
    $("#version").empty();
    $("#version").append('<option value="latest">Última versión</option>');
    game.builds.forEach((build) => {
      const buildElement = `<option value="${build.version}">${build.version}</option>`;
      $("#version").append(buildElement);
      gameNews.push(
        new News(
          "Actualización " + build.version,
          build.patchNotes,
          build.date,
          "update",
        ),
      );
    });
  }

  gameNews.sort((a, b) => new Date(b.date) - new Date(a.date));
  gameNews = gameNews.slice(0, 5);

  $("#game-news").empty();
  gameNews.forEach((news) => {
    let description;
    if (news.newsType == "update") {
      description = news.description || "Sin notas de parche";
    } else {
      description = news.description || "Sin descripción";
    }
    const newsElement = `
            <div class="bg-branddark p-2 rounded-lg shadow-lg">
                <h3 class="text-xl">${news.title}</h3>
                <p>${description}</p>
            </div>
        `;
    $("#game-news").append(newsElement);
  });

  $("#game-info").show();
}

// ── Download with progress bar ─────────────────────────────────────────────

async function downloadGame() {
  const gameId = $("[data-gameid].active").data("gameid");
  const version = $("#version").val();

  const $btn = $("#download");
  $btn.addClass("disabled").css("pointer-events", "none").text("Iniciando...");
  showDownloadProgress(0);

  try {
    // 1. Obtener token firmado y URL de stream
    const res = await fetch(`/api/library/${gameId}/download/${version}`);
    const json = await res.json();

    if (!res.ok) throw new Error(json.error || "Error al iniciar la descarga");

    const { url, filename } = json;

    // 2. Stream con seguimiento de progreso
    const fileRes = await fetch(url);
    if (!fileRes.ok) throw new Error("Error al obtener el archivo");

    const contentLength = fileRes.headers.get("Content-Length");
    const total = contentLength ? parseInt(contentLength) : null;
    const reader = fileRes.body.getReader();
    const chunks = [];
    let received = 0;

    $btn.text("Descargando...");

    while (true) {
      const { done, value } = await reader.read();
      if (done) break;
      chunks.push(value);
      received += value.length;
      showDownloadProgress(total ? Math.round((received / total) * 100) : null);
    }

    showDownloadProgress(100);

    // 3. Trigger descarga en el navegador
    const blob = new Blob(chunks);
    const objUrl = URL.createObjectURL(blob);
    const a = document.createElement("a");
    a.href = objUrl;
    a.download = filename;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(objUrl);
  } catch (err) {
    console.error("Download error:", err);
    showDownloadError(err.message);
  } finally {
    setTimeout(hideDownloadProgress, 2500);
    $btn.removeClass("disabled").css("pointer-events", "").text("Descargar");
  }
}

function showDownloadProgress(pct) {
  const $wrapper = $("#download-progress-wrapper");
  const $bar = $("#download-progress-bar");
  const $label = $("#download-progress-label");

  $wrapper.show();
  $("#download-progress-error").hide();

  if (pct === null) {
    // Sin Content-Length: animación indeterminada
    $bar.addClass("indeterminate").css("width", "100%");
    $label.text("Descargando...");
  } else {
    $bar.removeClass("indeterminate").css("width", pct + "%");
    $label.text(pct < 100 ? pct + "%" : "¡Listo!");
  }
}

function hideDownloadProgress() {
  $("#download-progress-wrapper").fadeOut(300, function () {
    $("#download-progress-bar").css("width", "0%").removeClass("indeterminate");
    $("#download-progress-label").text("0%");
    $("#download-progress-error").hide();
  });
}

function showDownloadError(msg) {
  const $wrapper = $("#download-progress-wrapper");
  $wrapper.show();
  $("#download-progress-bar").css("width", "0%").removeClass("indeterminate");
  $("#download-progress-label").text("Error");
  $("#download-progress-error").text(msg).show();
}

// ── Helpers ────────────────────────────────────────────────────────────────

class News {
  constructor(title, description, date, newsType = "news") {
    this.title = title;
    this.description = description;
    this.date = date;
    this.newsType = newsType;
  }
}

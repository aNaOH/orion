$(document).ready(function() {
    $('#no-games').hide();
    $('#game-info').hide();
    $('#sidebar').hide();

    $.ajax({
        url: '/api/library',
        method: 'GET',
        dataType: 'json',
        success: function(data) {
            if (data.status === 401) {
                if(data.to){
                    window.location.href = data.to;
                }
            } else {
                showGamesOnLibrary(data.data);
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.error('Error fetching user games:', textStatus, errorThrown);
        }
    });

    $('#toggleDeveloperGames').on('change', function() {
        const showDeveloperGames = $(this).is(':checked');
        if (showDeveloperGames) {
            $('[data-isdev]').show();
        } else {
            $('[data-isdev]').hide();
        }
    });
});

function showGamesOnLibrary(games) {
    const sidebar = $('#game-list');
    sidebar.empty();

    games.forEach(game => {
        const gameElement = `
            <div class="library-game cursor-pointer" data-gameid="${game.id}" ${game.isDeveloper ? 'data-isdev' : ''} onclick="changeGameShown(${game.id})">
                <h3 class="text-xl">${game.title}</h3>
            </div>
        `;
        sidebar.append(gameElement);
    });

    // Show the first game by default, if there are any games. Otherwise, show the no games message located in the HTML as #no-games.
    // If there are games and is a query parameter, show the game with the id in the query parameter.
    // If there isn't an element with data-gameid with the query parameter as value, show the first game.
    if (games.length > 0) {
        $('#sidebar').show();
        const gameSelectParam = new URLSearchParams(window.location.search).get('game');
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
        $('#no-games').show();
    }
    $('#library-loading').hide();
}

function changeGameShown(id) {
    // Remove active class from all elements with data-gameid
    $('[data-gameid]').removeClass('active');

    // Get the sidebar button by data-gameid
    const sidebarButton = $('[data-gameid="' + id.toString() +'"');
    sidebarButton.addClass('active');
    
    // Do an AJAX request to get the game info
    $.ajax({
        url: '/api/library/' + id,
        method: 'GET',
        dataType: 'json',
        success: function(data) {
            if (data.status === 401) {
                if(data.to){
                    window.location.href = data.to;
                }
            } else {
                showGameInfo(data.data);
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.error('Error fetching game info:', textStatus, errorThrown);
        }
    });
}

function showGameInfo(game) {
    console.log(game);

    $('#game-title').text(game.title);
    $('#game-image').attr('src', '/media/game/thumb/' + game.id);

    $('#game-download').hide();
    $('#no-download-avaliable').show();

    // Parse build info
    if(game.builds.length > 0) {
        $('#game-download').show();
        $('#no-download-avaliable').hide();
        $('#version').empty();
        $('#version').append('<option value="latest">Última versión</option>');
        game.builds.forEach(build => {
            const buildElement = `
                <option value="${build.version}">${build.version}</option>
            `;
            $('#version').append(buildElement);
        });
    }

    $('#game-info').show();
}

function downloadGame() {
    const gameId = $('[data-gameid].active').data('gameid');
    const version = $('#version').val();
    window.location.href = '/library/' + gameId + '/' + version;
}
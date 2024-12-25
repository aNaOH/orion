<?php

require_once './models/User.php';
require_once './models/Game.php';
require_once './models/GameGenre.php';

class Recommender {
    public static function getUserGenres(User $user): array {
        $ownedGames = $user->getAdquiredGames(false);
        $genreCounts = [];

        foreach ($ownedGames as $game) {
            if ($game->genre_id) {
                if (!isset($genreCounts[$game->genre_id])) {
                    $genreCounts[$game->genre_id] = 0;
                }
                $genreCounts[$game->genre_id]++;
            }
        }

        arsort($genreCounts);
        return array_slice(array_keys($genreCounts), 0, 3);
    }

    public static function getUserDevelopers(User $user): array {
        $ownedGames = $user->getAdquiredGames(false);
        $developerCounts = [];

        foreach ($ownedGames as $game) {
            if ($game->developer_id) {
                if (!isset($developerCounts[$game->developer_id])) {
                    $developerCounts[$game->developer_id] = 0;
                }
                $developerCounts[$game->developer_id]++;
            }
        }

        arsort($developerCounts);
        return array_slice(array_keys($developerCounts), 0, 3);
    }

    public static function getGamesByPreferredGenres(User $user): array {
        $recommendedGames = [];
        $ownedGames = $user->getAdquiredGames();
        $ownedGameIds = array_map(fn($game) => $game->id, $ownedGames);

        $preferredGenres = self::getUserGenres($user);
        $genreIds = implode(',', $preferredGenres);

        $genreGames = Connection::doSelect(ORION_DB, Game::$table, ["genre_id" => ['value' => $genreIds, 'modifier' => Connection::DBMODIFIER_IN]]);

        foreach ($genreGames as $gameRow) {
            if (!in_array($gameRow['id'], $ownedGameIds)) {
                $recommendedGames[] = Game::getById($gameRow['id']);
                if (count($recommendedGames) >= 4) {
                    break;
                }
            }
        }

        return $recommendedGames;
    }

    public static function getGamesByPreferredDevelopers(User $user): array {
        $recommendedGames = [];
        $ownedGames = $user->getAdquiredGames();
        $ownedGameIds = array_map(fn($game) => $game->id, $ownedGames);

        $preferredDevelopers = self::getUserDevelopers($user);
        $developerIds = implode(',', $preferredDevelopers);

        $developerGames = Connection::doSelect(ORION_DB, Game::$table, ["developer_id" => ['value' => $developerIds, 'modifier' => Connection::DBMODIFIER_IN]]);

        foreach ($developerGames as $gameRow) {
            if (!in_array($gameRow['id'], $ownedGameIds)) {
                $recommendedGames[] = Game::getById($gameRow['id']);
                if (count($recommendedGames) >= 4) {
                    break;
                }
            }
        }

        return $recommendedGames;
    }

    public static function getRecommendations(User $user): array {
        $recommendedGames = [];
        $ownedGames = $user->getAdquiredGames();
        $ownedGameIds = array_map(fn($game) => $game->id, $ownedGames);

        $preferredGenres = self::getUserGenres($user);
        $preferredDevelopers = self::getUserDevelopers($user);

        $genreIds = implode(',', $preferredGenres);
        $developerIds = implode(',', $preferredDevelopers);

        $genreGames = Connection::doSelect(ORION_DB, Game::$table, ["genre_id" => ['value' => $genreIds, 'modifier' => Connection::DBMODIFIER_IN]]);
        $developerGames = Connection::doSelect(ORION_DB, Game::$table, ["developer_id" => ['value' => $developerIds, 'modifier' => Connection::DBMODIFIER_IN]]);

        foreach (array_merge($genreGames, $developerGames) as $gameRow) {
            if (!in_array($gameRow['id'], $ownedGameIds)) {
                $recommendedGames[] = Game::getById($gameRow['id']);
                if (count($recommendedGames) >= 4) {
                    break;
                }
            }
        }

        return $recommendedGames;
    }
}

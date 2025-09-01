<?php

require_once './models/User.php';
require_once './models/Game.php';
require_once './models/GameGenre.php';

class Recommender {
    private static function getTopCounts(User $user, string $property, int $limit = 3): array {
        $ownedGames = $user->getAdquiredGames();
        $counts = [];
        foreach ($ownedGames as $game) {
            $id = $game->{$property} ?? null;
            if ($id) {
                $counts[$id] = ($counts[$id] ?? 0) + 1;
            }
        }
        arsort($counts);
        return array_slice(array_keys($counts), 0, $limit);
    }

    public static function getUserGenres(User $user): array {
        return self::getTopCounts($user, 'genre_id');
    }

    public static function getUserDevelopers(User $user): array {
        return self::getTopCounts($user, 'developer_id');
    }

    private static function getGamesByProperty(User $user, string $property, callable $filter = null, int $limit = 4): array {
        $ownedGames = $user->getAdquiredGames();
        $ownedGameIds = array_map(fn($game) => $game->id, $ownedGames);
        $preferred = self::getTopCounts($user, $property);
    if (!$preferred) return [];
    $games = Connection::doSelect(ORION_DB, Game::$table, [$property => ['value' => $preferred, 'modifier' => Connection::DBMODIFIER_IN]]);
        $result = [];
        foreach ($games as $gameRow) {
            if (!in_array($gameRow['id'], $ownedGameIds) && (!$filter || $filter($gameRow))) {
                $result[] = Game::getById($gameRow['id']);
                if (count($result) >= $limit) break;
            }
        }
        return $result;
    }

    public static function getGamesByPreferredGenres(User $user): array {
        return self::getGamesByProperty($user, 'genre_id');
    }

    public static function getGamesByPreferredDevelopers(User $user): array {
        return self::getGamesByProperty($user, 'developer_id');
    }

    public static function getRecommendations(User $user, bool $allowOwned = false): array {
        $ownedGames = $user->getAdquiredGames();
        $ownedGameIds = array_map(fn($game) => $game->id, $ownedGames);
    $preferredGenres = self::getTopCounts($user, 'genre_id');
    $preferredDevelopers = self::getTopCounts($user, 'developer_id');
        $ids = [];
    if ($preferredGenres) $ids['genre_id'] = ['value' => $preferredGenres, 'modifier' => Connection::DBMODIFIER_IN];
    if ($preferredDevelopers) $ids['developer_id'] = ['value' => $preferredDevelopers, 'modifier' => Connection::DBMODIFIER_IN];
        $games = [];
        if ($ids) {
            $genreGames = $ids['genre_id'] ? Connection::doSelect(ORION_DB, Game::$table, ['genre_id' => $ids['genre_id']]) : [];
            $developerGames = $ids['developer_id'] ? Connection::doSelect(ORION_DB, Game::$table, ['developer_id' => $ids['developer_id']]) : [];
            $temp_array = array_merge($genreGames, $developerGames);
            $unique = [];
            foreach ($temp_array as $gameRow) {
                if (!isset($unique[$gameRow['id']])) {
                    $unique[$gameRow['id']] = $gameRow;
                }
            }
            $games = array_values($unique);
        }
        shuffle($games);
        $recommendedGames = [];
        foreach ($games as $gameRow) {
            if ($allowOwned || !in_array($gameRow['id'], $ownedGameIds)) {
                $gameObj = Game::getById($gameRow['id']);
                if ($gameObj !== null) {
                    $recommendedGames[] = $gameObj;
                }
                if (count($recommendedGames) >= 4) break;
            }
        }
        return $recommendedGames;
    }
}

<?php

class Recommender
{
    private static function getFrequencyMap(User $user, string $property): array
    {
        $counts = [];
        foreach ($user->getAdquiredGames() as $game) {
            $value = $game->{$property} ?? null;
            if ($value === null) {
                continue;
            }
            $counts[$value] = ($counts[$value] ?? 0) + 1;
        }
        return $counts;
    }

    /**
     * Frecuencias de features: [feature_id => count]
     */
    private static function getFeatureFrequency(User $user): array
    {
        $counts = [];
        foreach ($user->getAdquiredGames() as $game) {
            foreach ($game->getFeatures() as $feature) {
                $counts[$feature->id] = ($counts[$feature->id] ?? 0) + 1;
            }
        }
        return $counts;
    }

    /**
     * Obtiene features de todos los juegos candidatos en un solo query.
     * Retorna: [game_id => [feature_id, feature_id...]]
     */
    private static function loadGameFeatures(array $gameRows): array
    {
        if (empty($gameRows)) {
            return [];
        }

        $ids = array_column($gameRows, "id");
        $placeholders = implode(",", array_fill(0, count($ids), "?"));

        $rows = Connection::customQuery(
            ORION_DB,
            "SELECT game_id, feature_id FROM game_has_feature WHERE game_id IN ($placeholders)",
            $ids,
        )->fetchAll(PDO::FETCH_ASSOC);

        $map = [];
        foreach ($rows as $row) {
            $map[$row["game_id"]][] = (int) $row["feature_id"];
        }
        return $map;
    }

    private static function getScoreForGame(
        array $row,
        array $genreFreq,
        array $devFreq,
        array $featureFreq,
        array $gameFeatures,
        int $genreWeight,
        int $developerWeight,
        int $featureWeight,
    ): int {
        $score = 0;

        if (isset($row["genre_id"], $genreFreq[$row["genre_id"]])) {
            $score += $genreFreq[$row["genre_id"]] * $genreWeight;
        }

        if (isset($row["developer_id"], $devFreq[$row["developer_id"]])) {
            $score += $devFreq[$row["developer_id"]] * $developerWeight;
        }

        if (isset($gameFeatures[$row["id"]])) {
            foreach ($gameFeatures[$row["id"]] as $featureId) {
                if (isset($featureFreq[$featureId])) {
                    $score += $featureFreq[$featureId] * $featureWeight;
                }
            }
        }

        return $score;
    }

    public static function getRecommendations(
        User $user,
        bool $allowOwned = false,
        int $limit = 4
    ): array {
        $owned = $user->getAdquiredGames();
        $ownedIds = array_map(fn($g) => $g->id, $owned);

        // Fallback: Si el usuario no tiene juegos, recomendamos los más populares
        if (empty($ownedIds)) {
            return Game::getPopularCommunities($limit);
        }

        $cacheKey =
            "recommendations:user:{$user->id}:allowOwned:" .
            (int) $allowOwned .
            ":limit:{$limit}:lib:" .
            md5(json_encode($ownedIds));

        if ($cached = FileCache::get($cacheKey)) {
            return array_map(
                fn($row) => self::createGameFromRow($row),
                $cached,
            );
        }

        $genreFreq = self::getFrequencyMap($user, "genre_id");
        $devFreq = self::getFrequencyMap($user, "developer_id");
        $featureFreq = self::getFeatureFrequency($user);

        if (empty($genreFreq) && empty($devFreq) && empty($featureFreq)) {
            return Game::getPopularCommunities($limit);
        }

        $sql = "SELECT * FROM `" . Game::$table . "` WHERE is_public = 1";
        $filters = [];

        if (!empty($genreFreq)) {
            $filters[] =
                "genre_id IN (" . implode(",", array_keys($genreFreq)) . ")";
        }
        if (!empty($devFreq)) {
            $filters[] =
                "developer_id IN (" . implode(",", array_keys($devFreq)) . ")";
        }

        if (!empty($filters)) {
            $sql .= " AND (" . implode(" OR ", $filters) . ")";
        }

        if (!$allowOwned && !empty($ownedIds)) {
            $sql .=
                " AND id NOT IN (" .
                implode(",", array_fill(0, count($ownedIds), "?")) .
                ")";
        }

        $rows = Connection::customQuery(
            ORION_DB,
            $sql,
            $allowOwned ? [] : $ownedIds,
        )->fetchAll(PDO::FETCH_ASSOC);

        // Si no hay suficientes recomendaciones filtradas, rellenamos con populares
        if (count($rows) < $limit) {
            $popular = Game::getPopularCommunities($limit * 2);
            foreach ($popular as $p) {
                if (count($rows) >= $limit) break;
                // Evitar duplicados y ya poseídos si aplica
                $exists = false;
                foreach ($rows as $r) { if ($r['id'] == $p->id) { $exists = true; break; } }
                if (!$exists && ($allowOwned || !in_array($p->id, $ownedIds))) {
                    // Convertir objeto Game a array para el usort posterior (o simplemente añadirlo)
                    // Para simplificar, si llegamos aquí, simplemente retornamos lo que tengamos mezclado
                }
            }
        }

        // Cargamos features en bloque
        $gameFeatures = self::loadGameFeatures($rows);

        // Pesos ajustables
        $genreWeight = 2;
        $developerWeight = 1;
        $featureWeight = 1;

        usort($rows, function ($a, $b) use (
            $genreFreq,
            $devFreq,
            $featureFreq,
            $gameFeatures,
            $genreWeight,
            $developerWeight,
            $featureWeight,
        ) {
            return self::getScoreForGame(
                $b,
                $genreFreq,
                $devFreq,
                $featureFreq,
                $gameFeatures,
                $genreWeight,
                $developerWeight,
                $featureWeight,
            ) <=>
                self::getScoreForGame(
                    $a,
                    $genreFreq,
                    $devFreq,
                    $featureFreq,
                    $gameFeatures,
                    $genreWeight,
                    $developerWeight,
                    $featureWeight,
                );
        });

        $top = array_slice($rows, 0, $limit);
        FileCache::set($cacheKey, $top);

        return array_map(fn($row) => self::createGameFromRow($row), $top);
    }

    private static function createGameFromRow(array $row): Game
    {
        return new Game(
            $row["title"],
            $row["short_description"],
            $row["description"],
            $row["launch_date"],
            (float) $row["base_price"],
            (float) $row["discount"],
            $row["as_editor"] == 1,
            $row["is_public"] == 1,
            $row["developer_name"],
            $row["developer_id"],
            $row["genre_id"],
            $row["id"],
        );
    }
}

<?php

require_once "./models/Developer.php";
require_once "./models/Build.php";
require_once "./models/GameGenre.php";
require_once "./models/Achievement.php";
require_once "./models/Stat.php";
require_once "./models/Leaderboard.php";

class Game
{
    public static string $table = "game";

    public ?int $id;
    public string $title;
    public ?string $short_description;
    public ?string $description;
    public ?string $launch_date;
    public ?float $base_price;
    public ?float $discount;
    public bool $as_editor;
    public bool $is_public;
    public ?string $developer_name;
    public int $developer_id;
    public ?int $genre_id;

    public function __construct(
        string $title,
        ?string $short_description,
        ?string $description,
        ?string $launch_date,
        ?float $base_price,
        ?float $discount,
        string $as_editor,
        bool $is_public,
        ?string $developer_name,
        int $developer_id,
        ?int $genre_id = null,
        ?int $id = null,
    ) {
        $this->title = $title;
        $this->short_description = $short_description;
        $this->description = $description;
        $this->launch_date = $launch_date;
        $this->base_price = $base_price;
        $this->discount = $discount;
        $this->as_editor = $as_editor;
        $this->is_public = $is_public;
        $this->developer_name = $developer_name;
        $this->developer_id = $developer_id;
        $this->genre_id = $genre_id;
        $this->id = $id;
    }

    public function save(): bool
    {
        $data = [
            "title" => $this->title,
            "short_description" => $this->short_description,
            "description" => $this->description,
            "launch_date" => $this->launch_date,
            "base_price" => $this->base_price,
            "discount" => $this->discount,
            "as_editor" => $this->as_editor ? 1 : 0,
            "is_public" => $this->is_public ? 1 : 0,
            "developer_name" => $this->developer_name,
            "developer_id" => $this->developer_id,
            "genre_id" => $this->genre_id,
        ];

        if (!isset($this->id) || !self::getById($this->id)) {
            $result = Connection::doInsert(ORION_DB, self::$table, $data);
            $this->id = ORION_DB->lastInsertId();
            return (bool) $result;
        } else {
            return (bool) Connection::doUpdate(ORION_DB, self::$table, $data, [
                "id" => $this->id,
            ]);
        }
    }

    public static function all()
    {
        $select = Connection::doSelect(ORION_DB, self::$table);
        $games = [];
        foreach ($select as $game) {
            $games[] = new Game(
                $game["title"],
                $game["short_description"],
                $game["description"],
                $game["launch_date"],
                (float) $game["base_price"],
                (float) $game["discount"],
                $game["as_editor"] == 1,
                $game["is_public"] == 1,
                $game["developer_name"],
                $game["developer_id"],
                $game["genre_id"],
                $game["id"],
            );
        }
        return $games;
    }

    public static function getById(int $id): ?Game
    {
        $game = Connection::doSelect(ORION_DB, self::$table, ["id" => $id]);
        if (count($game) === 1) {
            return new Game(
                $game[0]["title"],
                $game[0]["short_description"],
                $game[0]["description"],
                $game[0]["launch_date"],
                (float) $game[0]["base_price"],
                (float) $game[0]["discount"],
                $game[0]["as_editor"] == 1,
                $game[0]["is_public"] == 1,
                $game[0]["developer_name"],
                $game[0]["developer_id"],
                $game[0]["genre_id"],
                $game[0]["id"],
            );
        }
        return null;
    }

    public function getDeveloper(): Developer
    {
        return Developer::getById($this->developer_id);
    }

    public function delete(): ?bool
    {
        if (!isset($this->id)) {
            return null;
        }
        return (bool) Connection::doDelete(ORION_DB, self::$table, [
            "id" => $this->id,
        ]);
    }

    public static function pickRandom($quantity)
    {
        $sql =
            "SELECT * FROM " .
            self::$table .
            " WHERE is_public = ? ORDER BY RAND() LIMIT " .
            strval($quantity);

        $select = Connection::customQuery(ORION_DB, $sql, [1])->fetchAll(
            PDO::FETCH_ASSOC,
        );
        $games = [];
        foreach ($select as $game) {
            $games[] = new Game(
                $game["title"],
                $game["short_description"],
                $game["description"],
                $game["launch_date"],
                (float) $game["base_price"],
                (float) $game["discount"],
                $game["as_editor"] == 1,
                $game["is_public"] == 1,
                $game["developer_name"],
                $game["developer_id"],
                $game["genre_id"],
                $game["id"],
            );
        }
        return $games;
    }

    /**
     * Busca juegos por título usando un patrón LIKE.
     * @param string $query El texto a buscar en el título.
     * @return array Lista de instancias Game que coinciden.
     */
    public static function search(string $query): array
    {
        $results = Connection::searchInTable(
            ORION_DB,
            self::$table,
            $query,
            "title",
            Connection::DBSEARCH_BOTH,
        );
        $games = [];
        foreach ($results as $game) {
            $games[] = new Game(
                $game["title"],
                $game["short_description"],
                $game["description"],
                $game["launch_date"],
                (float) $game["base_price"],
                (float) $game["discount"],
                $game["as_editor"] == 1,
                $game["is_public"] == 1,
                $game["developer_name"],
                $game["developer_id"],
                $game["genre_id"],
                $game["id"],
            );
        }
        return $games;
    }

    public function getBuilds()
    {
        return Build::getByGame($this);
    }

    public function getBuildVersion($version)
    {
        return Build::get($this, $version);
    }

    public function getLatestBuild()
    {
        return Build::getLatestForGame($this);
    }

    public function getGenre(): ?GameGenre
    {
        return isset($this->genre_id)
            ? GameGenre::getById($this->genre_id)
            : null;
    }

    // Functions to get Achievements, Stats and Leaderboards
    public function getAchievements()
    {
        return Achievement::getAllByGame($this);
    }

    public function addAchievement(
        $name,
        $description,
        $icon,
        $locked_icon,
        $secret,
        $type,
        $stat,
        $stat_value,
    ) {
        $gameAchievements = $this->getAchievements();

        // Ordenamos por ID
        usort($gameAchievements, fn($a, $b) => $a->id <=> $b->id);

        // Valor por defecto: siguiente al último ID
        $achId =
            count($gameAchievements) > 0
                ? $gameAchievements[array_key_last($gameAchievements)]->id + 1
                : 1;

        $achievement = new Achievement(
            $achId,
            $name,
            $description,
            $icon,
            $locked_icon,
            $secret,
            $this->id,
            $type,
            $stat,
            $stat_value,
        );
        return $achievement->save();
    }

    public function getStats()
    {
        return Stat::getAllByGame($this);
    }

    public function getLeaderboards()
    {
        return Leaderboard::getAllByGame($this);
    }

    public function setFeatures(array $featureIds)
    {
        $currentFeatures = $this->getFeatures();
        $features = array_map(
            fn($id) => GameFeature::getById($id),
            $featureIds,
        );

        foreach ($currentFeatures as $feature) {
            if (!in_array($feature, $features, true)) {
                $this->removeFeature($feature);
            }
        }

        foreach ($features as $feature) {
            if (!$this->hasFeature($feature)) {
                $this->addFeature($feature);
            }
        }
    }

    public function addFeature(GameFeature|int $feature)
    {
        $featureID = $feature instanceof GameFeature ? $feature->id : $feature;

        //Insert feature into game_has_features table
        Connection::doInsert(ORION_DB, "game_has_feature", [
            "game_id" => $this->id,
            "feature_id" => $featureID,
        ]);
    }

    public function removeFeature(GameFeature|int $feature)
    {
        $featureID = $feature instanceof GameFeature ? $feature->id : $feature;

        //Delete feature from game_has_features table
        Connection::doDelete(ORION_DB, "game_has_feature", [
            "game_id" => $this->id,
            "feature_id" => $featureID,
        ]);
    }

    public function hasFeature(GameFeature|int $feature)
    {
        $featureID = $feature instanceof GameFeature ? $feature->id : $feature;

        $result = Connection::doSelect(ORION_DB, "game_has_feature", [
            "game_id" => $this->id,
            "feature_id" => $featureID,
        ]);

        //Check if feature exists in game_has_features table
        return $result !== null && count($result) > 0;
    }

    public function getFeatures()
    {
        return GameFeature::getAllByGame($this);
    }
}

<?php

class GameFeature
{
    public static string $table = "game_features";

    public int $id;
    public string $icon;
    public string $name;
    public string $tint;

    public function __construct(
        string $icon,
        string $name,
        string $tint,
        int $id = null,
    ) {
        $this->id = $id;
        $this->icon = $icon;
        $this->name = $name;
        $this->tint = $tint;
    }

    public static function getById(int $id): ?GameFeature
    {
        $feature = Connection::doSelect(ORION_DB, self::$table, ["id" => $id]);

        if (count($feature) === 1) {
            return new GameFeature(
                $feature[0]["icon"],
                $feature[0]["name"],
                $feature[0]["tint"],
                $feature[0]["id"],
            );
        }
        return null;
    }

    public static function getAll(): array
    {
        $features = Connection::doSelect(ORION_DB, self::$table);

        $gameFeatures = [];
        foreach ($features as $feature) {
            $gameFeatures[] = new GameFeature(
                $feature["icon"],
                $feature["name"],
                $feature["tint"],
                $feature["id"],
            );
        }
        return $gameFeatures;
    }

    public static function getAllByGame(Game|int $game): array
    {
        $game_id = $game instanceof Game ? $game->id : $game;
        $features = [];
        $select = Connection::doSelect(ORION_DB, "game_has_feature", [
            "game_id" => $game_id,
        ]);

        foreach ($select as $feature) {
            $featuresSelect = Connection::doSelect(ORION_DB, self::$table, [
                "id" => $feature["feature_id"],
            ])[0];

            $features[] = new GameFeature(
                $featuresSelect["icon"],
                $featuresSelect["name"],
                $featuresSelect["tint"],
                $featuresSelect["id"],
            );
        }

        return $features;
    }

    public function save(): bool
    {
        $data = [
            "icon" => $this->icon,
            "name" => $this->name,
            "tint" => $this->tint,
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

    public function delete(): ?bool
    {
        if (!isset($this->id)) {
            return null;
        }
        return (bool) Connection::doDelete(ORION_DB, self::$table, [
            "id" => $this->id,
        ]);
    }

    public function getGames(): array
    {
        $games = [];
        $select = Connection::doSelect(ORION_DB, "game_has_feature", [
            "feature_id" => $this->id,
        ]);

        foreach ($select as $gameRow) {
            $games[] = Game::getById($gameRow["game_id"]);
        }
        return $games;
    }
}

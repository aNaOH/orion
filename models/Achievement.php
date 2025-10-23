<?php

require_once "models/Game.php";

class Achievement
{
    public static string $table = "achievements";

    public ?int $id;
    public string $name;
    public string $description;
    public string $icon;
    public ?string $locked_icon;
    public bool $secret;
    public ?int $game_id;
    public EACHIEVEMENT_TYPE $type;
    public ?int $stat_id;
    public ?int $stat_value;

    public function __construct(
        ?int $id,
        string $name,
        string $description,
        string $icon,
        ?string $locked_icon,
        bool $secret,
        ?int $game_id,
        EACHIEVEMENT_TYPE|int $type,
        ?int $stat_id = null,
        ?int $stat_value = null,
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->icon = $icon;
        $this->locked_icon = $locked_icon;
        $this->secret = $secret;
        $this->game_id = $game_id;
        $this->type = is_numeric($type)
            ? EACHIEVEMENT_TYPE::from($type)
            : $type;
        $this->stat_id = $stat_id;
        $this->stat_value = $stat_value;
    }

    public static function getById(Game|int $game, int $id): ?Achievement
    {
        $gameId = $game instanceof Game ? $game->id : $game;

        $achievement = Connection::doSelect(ORION_DB, self::$table, [
            "id" => $id,
            "game_id" => $gameId,
        ]);

        if (count($achievement) === 1) {
            return new Achievement(
                $achievement[0]["id"],
                $achievement[0]["name"],
                $achievement[0]["description"],
                $achievement[0]["icon"],
                $achievement[0]["locked_icon"],
                $achievement[0]["secret"],
                $achievement[0]["game_id"],
                EACHIEVEMENT_TYPE::from($achievement[0]["type"]),
                $achievement[0]["stat_id"],
                $achievement[0]["stat_value"],
            );
        }
        return null;
    }

    public static function getAllByStat(Game|int $game, int $statId): array
    {
        $gameId = $game instanceof Game ? $game->id : $game;
        $achievements = [];
        $select = Connection::doSelect(ORION_DB, self::$table, [
            "stat_id" => $statId,
        ]);

        foreach ($select as $achievementRow) {
            $achievements[] = Achievement::getById(
                $gameId,
                $achievementRow["id"],
            );
        }
        return $achievements;
    }

    public static function getAllByGame(Game|int $game): array
    {
        $game_id = $game instanceof Game ? $game->id : $game;
        $achievements = [];
        $select = Connection::doSelect(ORION_DB, self::$table, [
            "game_id" => $game_id,
        ]);

        foreach ($select as $achievementRow) {
            $achievements[] = new Achievement(
                $achievementRow["id"],
                $achievementRow["name"],
                $achievementRow["description"],
                $achievementRow["icon"],
                $achievementRow["locked_icon"],
                $achievementRow["secret"],
                $achievementRow["game_id"],
                EACHIEVEMENT_TYPE::from($achievementRow["type"]),
                $achievementRow["stat_id"],
                $achievementRow["stat_value"],
            );
        }
        return $achievements;
    }

    public function save(): bool
    {
        $data = [
            "name" => $this->name,
            "description" => $this->description,
            "icon" => $this->icon,
            "locked_icon" => $this->locked_icon,
            "secret" => intval($this->secret),
            "type" => $this->type->value,
            "stat_id" => $this->stat_id,
            "stat_value" => $this->stat_value,
        ];

        if (self::getById($this->game_id, $this->id) == null) {
            $data["game_id"] = $this->game_id;
            $data["id"] = $this->id;
            $result = Connection::doInsert(ORION_DB, self::$table, $data);
            return (bool) $result;
        } else {
            return (bool) Connection::doUpdate(ORION_DB, self::$table, $data, [
                "id" => $this->id,
            ]);
        }
    }

    public function delete(): bool
    {
        if (self::getById($this->game_id, $this->id) == null) {
            return false;
        }

        return (bool) Connection::doDelete(ORION_DB, self::$table, [
            "id" => $this->id,
        ]);
    }

    public function getGame(): ?Game
    {
        return isset($this->game_id) ? Game::getById($this->game_id) : null;
    }

    public function getStat(): ?Stat
    {
        return isset($this->stat_id) ? Stat::getById($this->stat_id) : null;
    }
}

<?php

require_once "models/Game.php";

class Achievement {
    public static string $table = 'achievements';

    public int $id;
    public string $name;
    public string $description;
    public string $icon;
    public ?string $locked_icon;
    public bool $secret;
    public ?int $game_id;

    public function __construct(int $id, string $name, string $description, string $icon, ?string $locked_icon, bool $secret, ?int $game_id) {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->icon = $icon;
        $this->locked_icon = $locked_icon;
        $this->secret = $secret;
        $this->game_id = $game_id;
    }

    public static function getById(int $id): ?Achievement {
        $achievement = Connection::doSelect(ORION_DB, self::$table, ["id" => $id]);

        if (count($achievement) === 1) {
            return new Achievement(
                $achievement[0]['id'],
                $achievement[0]['name'],
                $achievement[0]['description'],
                $achievement[0]['icon'],
                $achievement[0]['locked_icon'],
                $achievement[0]['secret'],
                $achievement[0]['game_id']
            );
        }
        return null;
    }

    public function save(): bool {
        $data = [
            'name' => $this->name,
            'description' => $this->description,
            'icon' => $this->icon,
            'locked_icon' => $this->locked_icon,
            'secret' => $this->secret,
            'game_id' => $this->game_id,
        ];

        if (!isset($this->id) || !self::getById($this->id)) {
            $result = Connection::doInsert(ORION_DB, self::$table, $data);
            $this->id = ORION_DB->lastInsertId();
            return (bool)$result;
        } else {
            return (bool)Connection::doUpdate(ORION_DB, self::$table, $data, ['id' => $this->id]);
        }
    }

    public function delete(): ?bool {
        if (!isset($this->id)) return null;
        return (bool)Connection::doDelete(ORION_DB, self::$table, ['id' => $this->id]);
    }

    public function getGame(): ?Game {
        return isset($this->game_id) ? Game::getById($this->game_id) : null;
    }
}

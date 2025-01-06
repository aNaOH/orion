<?php

require_once "models/Game.php";

class Stat {
    public static string $table = 'stat';

    public int $id;
    public int $game_id;
    public string $name;
    public int $number;
    public ESTAT_TYPE $type;

    public function __construct(int $id, int $game_id, string $name, int $number, ESTAT_TYPE|int $type) {
        $this->id = $id;
        $this->game_id = $game_id;
        $this->name = $name;
        $this->number = $number;
        $this->type = is_numeric($type) ? ESTAT_TYPE::from($type) : $type;
    }

    public static function getById(int $id): ?Stat {
        $stat = Connection::doSelect(ORION_DB, self::$table, ["id" => $id]);

        if (count($stat) === 1) {
            return new Stat(
                $stat[0]['id'],
                $stat[0]['game_id'],
                $stat[0]['name'],
                $stat[0]['number'],
                ESTAT_TYPE::from($stat[0]['type'])
            );
        }
        return null;
    }

    public static function getAllByGame(Game|int $game): array {
        $game_id = $game instanceof Game ? $game->id : $game;
        $stats = [];
        $select = Connection::doSelect(ORION_DB, self::$table, ["game_id" => $game_id]);

        foreach ($select as $statRow) {
            $stats[] = new Stat(
                $statRow['id'],
                $statRow['game_id'],
                $statRow['name'],
                $statRow['number'],
                ESTAT_TYPE::from($statRow['type'])
            );
        }
        return $stats;
    }

    public function save(): bool {
        $data = [
            'game_id' => $this->game_id,
            'name' => $this->name,
            'number' => $this->number,
            'type' => $this->type->value,
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

    public function getUserStats(): array {
        $userStats = [];
        $select = Connection::doSelect(ORION_DB, "has_stat", ["stat_id" => $this->id]);

        foreach ($select as $userStatRow) {
            $userStats[] = [
                'user' => User::getById($userStatRow['user_id']),
                'value' => $userStatRow['value']
            ];
        }
        return $userStats;
    }
}

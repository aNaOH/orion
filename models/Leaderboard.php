<?php

require_once "models/Game.php";
require_once "models/Stat.php";

class Leaderboard {
    public static string $table = 'leaderboards';

    public int $id;
    public int $concept;
    public ?int $game_id;
    public ?int $stat_id;

    public function __construct(int $id, int $concept, ?int $game_id, ?int $stat_id) {
        $this->id = $id;
        $this->concept = $concept;
        $this->game_id = $game_id;
        $this->stat_id = $stat_id;
    }

    public static function getById(int $id): ?Leaderboard {
        $leaderboard = Connection::doSelect(ORION_DB, self::$table, ["id" => $id]);

        if (count($leaderboard) === 1) {
            return new Leaderboard(
                $leaderboard[0]['id'],
                $leaderboard[0]['concept'],
                $leaderboard[0]['game_id'],
                $leaderboard[0]['stat_id']
            );
        }
        return null;
    }

    public function save(): bool {
        $data = [
            'concept' => $this->concept,
            'game_id' => $this->game_id,
            'stat_id' => $this->stat_id,
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

    public function getStat(): ?Stat {
        return isset($this->stat_id) ? Stat::getById($this->stat_id) : null;
    }
}

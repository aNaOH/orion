<?php 

require_once "models/Game.php";

class Build {
    public static string $table = 'builds';

    public int $game_id;
    public string $file;
    public string $version;
    public ?string $release_date;

    public static function get(Game|int $game, string $version){

        $game_id = $game instanceof Game ? $game->id : $game;
        $build = Connection::doSelect(ORION_DB, self::$table, [
            "game_id" => $game_id,
            "version" => $version
        ]);

        if (count($build) === 1) {
            return new Build(
                $build[0]['game_id'],
                $build[0]['file'],
                $build[0]['version'],
                $build[0]['release_date'],
            );
        }
        return null;
    }

    public static function getByGame(Game|int $game){

        $game_id = $game instanceof Game ? $game->id : $game;
        $buildDB = Connection::doSelect(ORION_DB, self::$table, [
            "game_id" => $game_id
        ]);

        $builds = [];

        if (count($buildDB) > 0) {
            foreach ($buildDB as $build) {
                $builds[] = new Build(
                    $build['game_id'],
                    $build['file'],
                    $build['version'],
                    $build['release_date'],
                );
            }
        }
        return $builds;
    }

    public function __construct(int $game_id, string $file, string $version, string $release_date = null) {
        $this->game_id = $game_id;
        $this->file = $file;
        $this->version = $version;
        $this->release_date = $release_date;
    }

    public function getParentGame() {
        return Game::getById($this->game_id);
    }

    public function save(): bool {
        $data = [
            'game_id' => $this->game_id,
            'file' => $this->file,
            'version' => $this->version,
        ];

        if (!isset($this->release_date) || !self::get($this->game_id, $this->version)) {
            $result = Connection::doInsert(ORION_DB, self::$table, $data);
            return (bool)$result;
        } else {
            return (bool)Connection::doUpdate(ORION_DB, self::$table, $data, ['game_id' => $this->game_id, 'version' => $this->version]);
        }
    }
}
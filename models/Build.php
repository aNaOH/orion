<?php 

require_once "models/Game.php";

class Build {
    public static string $table = 'builds';

    public int $game_id;
    private ?string $file;
    public string $version;
    public ?string $release_date;
    public ?string $patch_notes;

    public static function get(Game|int $game, string $version){

        $game_id = $game instanceof Game ? $game->id : $game;
        $build = Connection::doSelect(ORION_DB, self::$table, [
            "game_id" => $game_id,
            "version" => $version
        ]);

        if (count($build) === 1) {
            return new Build(
                $build[0]['game_id'],
                $build[0]['version'],
                $build[0]['file'],
                $build[0]['release_date'],
                $build[0]['patch_notes']
            );
        }
        return null;
    }

    public static function getByGame(Game|int $game){

        $game_id = $game instanceof Game ? $game->id : $game;
        //Turn this into a custom query to use ORDER BY
        $buildDB = Connection::customQuery(ORION_DB, "SELECT * FROM ".self::$table." WHERE game_id = ? ORDER BY release_date DESC",[$game_id])->fetchAll(PDO::FETCH_ASSOC);

        $builds = [];

        if (count($buildDB) > 0) {
            foreach ($buildDB as $build) {
                $builds[] = new Build(
                    $build['game_id'],
                    $build['version'],
                    $build['file'],
                    $build['release_date'],
                    $build['patch_notes']
                );
            }
        }
        return $builds;
    }

    public static function getLatestForGame(Game|int $game){

        $game_id = $game instanceof Game ? $game->id : $game;
        $build = Connection::customQuery(ORION_DB, "SELECT * FROM ".self::$table." WHERE game_id = ? ORDER BY release_date DESC LIMIT 1",[$game_id])->fetchAll(PDO::FETCH_ASSOC);

        if (count($build) === 1) {
            return new Build(
                $build[0]['game_id'],
                $build[0]['version'],
                $build[0]['file'],
                $build[0]['release_date'],
                $build[0]['patch_notes']
            );
        }
        return null;
    }

    public function __construct(int $game_id, string $version, string $file = null, string $release_date = null, string $patch_notes = null) {
        $this->game_id = $game_id;
        $this->file = $file;
        $this->version = $version;
        $this->release_date = $release_date;
        $this->patch_notes = $patch_notes;
    }

    public function getParentGame() {
        return Game::getById($this->game_id);
    }

    public function save(): bool {
        $data = [
            'game_id' => $this->game_id,
            'file' => $this->file,
            'version' => $this->version,
            'patch_notes' => $this->patch_notes,
        ];

        if (!isset($this->release_date) || !self::get($this->game_id, $this->version)) {
            $result = Connection::doInsert(ORION_DB, self::$table, $data);
            return (bool)$result;
        } else {
            return (bool)Connection::doUpdate(ORION_DB, self::$table, $data, ['game_id' => $this->game_id, 'version' => $this->version]);
        }
    }
    public function getUUID() {
        return Tript::encryptString("buildg".strval($this->getParentGame()->id)."v".$this->version);
    }

    public function setFile($file) {
        $uuid = $this->getUUID();
        $this->file = $uuid;
        return S3Helper::upload(EBUCKET_LOCATION::GAME_BUILD, $uuid, null, $file['type'], $file['tmp_name']);
    }

    public function getFile() {
        var_dump($this->file);
        return is_null($this->file) ? null : S3Helper::retrieve(EBUCKET_LOCATION::GAME_BUILD, $this->file);
    }
}
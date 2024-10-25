<?php

class Game {
    private static string $table = 'game';

    public ?int $id;
    public string $title;
    public ?string $short_description;
    public ?string $description;
    public ?string $launch_date;
    public ?float $base_price;
    public ?float $discount;
    public ?string $file;
    public ?string $version;
    public int $developer_id;

    public function __construct(
        string $title,
        ?string $short_description,
        ?string $description,
        ?string $launch_date,
        ?float $base_price,
        ?float $discount,
        ?string $file,
        ?string $version,
        int $developer_id,
        ?int $id = null
    ) {
        $this->title = $title;
        $this->short_description = $short_description;
        $this->description = $description;
        $this->launch_date = $launch_date;
        $this->base_price = $base_price;
        $this->discount = $discount;
        $this->file = $file;
        $this->version = $version;
        $this->developer_id = $developer_id;
        $this->id = $id;
    }

    public function save(): bool {
        $data = [
            'title' => $this->title,
            'short_description' => $this->short_description,
            'description' => $this->description,
            'launch_date' => $this->launch_date,
            'base_price' => $this->base_price,
            'discount' => $this->discount,
            'file' => $this->file,
            'version' => $this->version,
            'developer_id' => $this->developer_id
        ];

        if (!isset($this->id) || !self::getById($this->id)) {
            $result = Connection::doInsert(ORION_DB, self::$table, $data);
            $this->id = ORION_DB->lastInsertId();
            return (bool)$result;
        } else {
            return (bool)Connection::doUpdate(ORION_DB, self::$table, $data, ['id' => $this->id]);
        }
    }

    public static function getById(int $id): ?Game {
        $game = Connection::doSelect(ORION_DB, self::$table, ['id' => $id]);
        if (count($game) === 1) {
            return new Game(
                $game[0]['title'],
                $game[0]['short_description'],
                $game[0]['description'],
                $game[0]['launch_date'],
                (float)$game[0]['base_price'],
                (float)$game[0]['discount'],
                $game[0]['file'],
                $game[0]['version'],
                $game[0]['developer_id'],
                $game[0]['id']
            );
        }
        return null;
    }

    public function delete(): ?bool {
        if (!isset($this->id)) return null;
        return (bool)Connection::doDelete(ORION_DB, self::$table, ['id' => $this->id]);
    }
}

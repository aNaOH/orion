<?php

class GameNewsCategory {
    public static string $table = 'game_news_categories';

    public int $id;
    public string $name;
    public string $tint;

    public function __construct(int $id, string $name, string $tint) {
        $this->id = $id;
        $this->name = $name;
        $this->tint = $tint;
    }

    public static function getById(int $id): ?GameNewsCategory {
        $category = Connection::doSelect(ORION_DB, self::$table, ["id" => $id]);

        if (count($category) === 1) {
            return new GameNewsCategory(
                $category[0]['id'],
                $category[0]['name'],
                $category[0]['tint']
            );
        }
        return null;
    }

    public function save(): bool {
        $data = [
            'name' => $this->name,
            'tint' => $this->tint,
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
}

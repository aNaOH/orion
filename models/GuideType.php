<?php

class GuideType {
    public static string $table = 'guide_types';

    public ?int $id;
    public string $icon;
    public string $type;
    public string $tint;


    public function __construct(string $icon, string $type, string $tint, ?int $id = null) {
        $this->icon = $icon;
        $this->type = $type;
        $this->tint = $tint;

        $this->id = $id;
    }

    public static function getById(int $id): ?GuideType {
        $gType = Connection::doSelect(ORION_DB, self::$table, ["id" => $id]);

        if (count($gType) === 1) {
            return new GuideType(
                $gType[0]['icon'], 
                $gType[0]['type'],
                $gType[0]['tint'],
                $gType[0]['id']
            );
        }
        return null;
    }

    public static function getAll(): array {
        $gTypesDB = Connection::doSelect(ORION_DB, self::$table);

        $gTypes = [];

        foreach ($gTypesDB as $gType) {
            $gTypes[] = new GuideType(
                $gType['icon'], 
                $gType['type'],
                $gType['tint'],
                $gType['id']
            );
        }

        return $gTypes;
    }

    public function save(): bool {
        $data = [
            'icon' => $this->icon,
            'type' => $this->type,
            'tint' => $this->tint
        ];

        if (!isset($this->id)) {
            return (bool)Connection::doInsert(ORION_DB, self::$table, $data);
        } else {
            return (bool)Connection::doUpdate(ORION_DB, self::$table, $data, ['id' => $this->id]);
        }
    }


    public function delete(): ?bool {
        return (bool)Connection::doDelete(ORION_DB, self::$table, ['id' => $this->id]);
    }
}
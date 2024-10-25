<?php

class Developer {
    private static string $table = 'developers';

    public ?int $id;
    public string $name;
    public ?string $profile_pic;
    public ?string $motd;
    public int $owner_id;

    public function __construct(
        string $name,
        ?string $profile_pic,
        ?string $motd,
        int $owner_id,
        ?int $id = null
    ) {
        $this->name = $name;
        $this->profile_pic = $profile_pic;
        $this->motd = $motd;
        $this->owner_id = $owner_id;
        $this->id = $id;
    }

    public function save(): bool {
        $data = [
            'name' => $this->name,
            'profile_pic' => $this->profile_pic,
            'motd' => $this->motd,
            'owner_id' => $this->owner_id
        ];

        if (!isset($this->id) || !self::getById($this->id)) {
            $result = Connection::doInsert(ORION_DB, self::$table, $data);
            $this->id = ORION_DB->lastInsertId();
            return (bool)$result;
        } else {
            return (bool)Connection::doUpdate(ORION_DB, self::$table, $data, ['id' => $this->id]);
        }
    }

    public static function getById(int $id): ?Developer {
        $developer = Connection::doSelect(ORION_DB, self::$table, ['id' => $id]);
        if (count($developer) === 1) {
            return new Developer(
                $developer[0]['name'],
                $developer[0]['profile_pic'],
                $developer[0]['motd'],
                $developer[0]['owner_id'],
                $developer[0]['id']
            );
        }
        return null;
    }

    public function delete(): ?bool {
        if (!isset($this->id)) return null;
        return (bool)Connection::doDelete(ORION_DB, self::$table, ['id' => $this->id]);
    }
}

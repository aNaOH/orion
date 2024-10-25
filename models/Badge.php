<?php

class Badge {
    public static $table = 'badges';

    public $id;
    public $name;
    public $description;
    public $icon;
    public $game_id;

    //Constructor
    public function __construct($name, $description, $icon, $game_id, $id = null) {
        $this->name = $name;
        $this->description = $description;
        $this->icon = $icon;
        $this->game_id = $game_id;
        $this->id = $id;
    }

    //Get by
    public static function getById($id) {
        $badge = Connection::doSelect(ORION_DB, self::$table, ['id' => $id]);
        if (count($badge) === 1) {
            return new Badge(
                $badge[0]['name'],
                $badge[0]['description'],
                $badge[0]['icon'],
                $badge[0]['game_id'],
                $badge[0]['id']
            );
        }
        return null;
    }

    //DB functions
    public function save() {
        $data = [
            'name' => $this->name,
            'description' => $this->description,
            'icon' => $this->icon,
            'game_id' => $this->game_id
        ];

        if (!isset($this->id) || !self::getById($this->id)) {
            $result = Connection::doInsert(ORION_DB, self::$table, $data);
            $this->id = ORION_DB->lastInsertId();
            return $result;
        } else {
            return Connection::doUpdate(ORION_DB, self::$table, $data, ['id' => $this->id]);
        }
    }

    public function delete() {
        if (!isset($this->id)) return null;
        return Connection::doDelete(ORION_DB, self::$table, ['id' => $this->id]);
    }

    //Relationship with User
    public function hasUserUnlocked(User|int $user, ?string &$dateUnlocked = null): bool {
        $userId = $user instanceof User ? $user->id : $user;
        $select = Connection::doSelect(ORION_DB, User::$table, [
            "badge_id" => $this->id,
            "user_id" => $userId
        ]);

        if (count($select) === 1) {
            $dateUnlocked = $select[0]['date'];
            return true;
        }

        return false;
    }

    public function getUserUnlockedCount(): int {
        $select = Connection::doSelect(ORION_DB, User::$table, ["badge_id" => $this->id]);
        return count($select);
    }

    public function getUserUnlockedDate(User|int $user): ?string {
        $userId = $user instanceof User ? $user->id : $user;
        $select = Connection::doSelect(ORION_DB, User::$table, [
            "badge_id" => $this->id,
            "user_id" => $userId
        ]);

        return count($select) === 1 ? $select[0]['date'] : null;
    }
}

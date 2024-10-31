<?php

require_once './models/Badge.php';
require_once './models/Developer.php';

class User {
    public static string $table = 'users';

    public ?int $id;
    public string $email;
    public string $username;
    public string $password;
    public EUSER_TYPE $role;
    public ?string $profile_pic;
    public ?string $motd;
    public ?int $badge;

    // Constructor
    public function __construct(
        string $email, 
        string $username, 
        string $password, 
        EUSER_TYPE|int $role, 
        ?string $profile_pic = null, 
        ?string $motd = null, 
        ?int $badge = null, 
        ?int $id = null
    ) {
        $this->role = is_numeric($role) ? EUSER_TYPE::from($role) : $role;
        $this->email = $email;
        $this->username = $username;
        $this->password = $password;
        $this->profile_pic = $profile_pic;
        $this->motd = $motd;
        $this->badge = $badge;
        $this->id = $id;
    }

    // Get by ID
    public static function getById(int $id): ?User {
        $user = Connection::doSelect(ORION_DB, self::$table, ["id" => $id]);

        if (count($user) === 1) {
            return new User(
                $user[0]['email'], 
                $user[0]['username'], 
                $user[0]['password'], 
                $user[0]['role'], 
                $user[0]['profile_pic'], 
                $user[0]['motd'], 
                $user[0]['badge_id'], 
                $user[0]['id']
            );
        }
        return null;
    }

    public static function getByEmail(string $email): ?User {
        $user = Connection::doSelect(ORION_DB, self::$table, ["email" => $email]);

        if (count($user) === 1) {
            return new User(
                $user[0]['email'], 
                $user[0]['username'], 
                $user[0]['password'], 
                $user[0]['role'], 
                $user[0]['profile_pic'], 
                $user[0]['motd'], 
                $user[0]['badge_id'], 
                $user[0]['id']
            );
        }
        return null;
    }

    // Handle methods
    public function getHandle(): string {
        return strtolower(str_replace(" ", "_", $this->username)) . "#" . strval($this->id);
    }

    public static function getByHandle(string $handle): ?User {
        $id = explode("#", $handle)[1] ?? null;
        if (!$id) return null;

        $user = Connection::doSelect(ORION_DB, self::$table, ["id" => $id]);
        if (count($user) === 1) {
            $userObj = new User(
                $user[0]['email'], 
                $user[0]['username'], 
                $user[0]['password'], 
                $user[0]['role'], 
                $user[0]['profile_pic'], 
                $user[0]['motd'], 
                $user[0]['badge_id'], 
                $user[0]['id']
            );
            return $userObj->getHandle() === $handle ? $userObj : null;
        }
        return null;
    }

    // DB functions
    public function save(): bool {
        $data = [
            'email' => $this->email,
            'username' => $this->username,
            'password' => password_hash($this->password, PASSWORD_BCRYPT),
            'role' => $this->role->value,
            'profile_pic' => $this->profile_pic,
            'motd' => $this->motd,
            'badge_id' => $this->badge,
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

    // Profile picture URL getter
    public function getProfilePicURL(): string {
        return "/media/profile/" . ($this->profile_pic ?? "default");
    }

    // Relationship with Developer
    public function getDeveloperInfo() : ?Developer {
        return Developer::getByUser($this);
    }

    public function addDeveloperInfo($name) : bool {
        if(is_null($this->getDeveloperInfo())){
            $dev = new Developer($name, null, null, $this->id);
            return $dev->save();
        }

        return false;
    }

    // Relationship with Badge
    public function getBadge(): ?Badge {
        return isset($this->badge) && is_numeric($this->badge) ? Badge::getById($this->badge) : null;
    }

    public function hasUnlockedBadge(Badge|int $badge, ?string &$dateUnlocked = null): bool {
        $badgeId = $badge instanceof Badge ? $badge->id : $badge;
        $select = Connection::doSelect(ORION_DB, "badge_unlocked", ["badge_id" => $badgeId, "user_id" => $this->id]);
        
        if (count($select) === 1) {
            $dateUnlocked = $select[0]['date'];
            return true;
        }
        return false;
    }

    public function getUnlockedBadges(): array {
        $badges = [];
        $select = Connection::doSelect(ORION_DB, "badge_unlocked", ["user_id" => $this->id]);
        
        foreach ($select as $badgeRow) {
            $badges[] = Badge::getById($badgeRow['id']);
        }
        return $badges;
    }

    public function getUnlockedBadgeDate(Badge|int $badge): ?string {
        $badgeId = $badge instanceof Badge ? $badge->id : $badge;
        $select = Connection::doSelect(ORION_DB, "badge_unlocked", ["badge_id" => $badgeId, "user_id" => $this->id]);

        return count($select) === 1 ? $select[0]['date'] : null;
    }

    public function unlockBadge(Badge|int $badge) : bool {
        if(!$this->hasUnlockedBadge($badge)){
            $badgeId = $badge instanceof Badge ? $badge->id : $badge;
            return Connection::doInsert(ORION_DB, 'badge_unlocked', [
                "badge_id" => $badgeId,
                "user_id" => $this->id
            ]);
        }

        return false;
    }

    // Auth related
    public function toSessionArray(): array {
        return isset($this->id) ? [
            "id" => $this->id,
            "username" => $this->username,
            "profile_pic" => $this->profile_pic
        ] : [];
    }
}

<?php

require_once './models/Badge.php';
require_once './models/Developer.php';
require_once './models/Game.php';

class User {
    public static string $table = 'users';

    public ?int $id;
    public string $email;
    public string $username;
    private string $password;
    public string $birthdate;
    public EUSER_TYPE $role;
    public ?string $profile_pic;
    public ?string $motd;
    public ?int $badge;
    public ?string $created_at;
    public bool $is_archived;

    // Constructor
    public function __construct(
        string $email, 
        string $username, 
        string $password,
        string $birthdate, 
        EUSER_TYPE|int $role, 
        ?string $profile_pic = null, 
        ?string $motd = null, 
        ?int $badge = null, 
        ?int $id = null,
        ?string $created_at = null,
        bool $is_archived = false
    ) {
        $this->role = is_numeric($role) ? EUSER_TYPE::from($role) : $role;
        $this->email = $email;
        $this->username = $username;
        $this->password = $password;
        $this->birthdate = $birthdate;
        $this->profile_pic = $profile_pic;
        $this->motd = $motd;
        $this->badge = $badge;
        $this->id = $id;
        $this->created_at = $created_at;
        $this->is_archived = $is_archived;
    }

    // Get by ID
    public static function getById(int $id): ?User {
        $user = Connection::doSelect(ORION_DB, self::$table, ["id" => $id]);

        if (count($user) === 1) {
            return new User(
                $user[0]['email'], 
                $user[0]['username'], 
                $user[0]['password'], 
                $user[0]['birthdate'], 
                $user[0]['role'], 
                $user[0]['profile_pic'], 
                $user[0]['motd'], 
                $user[0]['badge_id'], 
                $user[0]['id'],
                $user[0]['created_at'],
                $user[0]['is_archived']
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
                $user[0]['birthdate'], 
                $user[0]['role'], 
                $user[0]['profile_pic'], 
                $user[0]['motd'], 
                $user[0]['badge_id'], 
                $user[0]['id'],
                $user[0]['created_at'],
                $user[0]['is_archived']
            );
        }
        return null;
    }

    public function getPassword() {
        return $this->password;
    }

    public function setPassword($password) {
        $this->password = password_hash($password, PASSWORD_BCRYPT);
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
                $user[0]['birthdate'], 
                $user[0]['role'], 
                $user[0]['profile_pic'], 
                $user[0]['motd'], 
                $user[0]['badge_id'], 
                $user[0]['id'],
                $user[0]['created_at'],
                $user[0]['is_archived']
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
            'birthdate' => $this->birthdate,
            'role' => $this->role->value,
            'profile_pic' => $this->profile_pic,
            'motd' => $this->motd,
            'badge_id' => $this->badge,
            'is_archived' => $this->is_archived,
        ];

        if (!isset($this->id) || !self::getById($this->id)) {
            $this->setPassword($this->password);
            $data['password'] = $this->password;
            $result = Connection::doInsert(ORION_DB, self::$table, $data);
            $this->id = ORION_DB->lastInsertId();
            return (bool)$result;
        } else {
            return (bool)Connection::doUpdate(ORION_DB, self::$table, $data, ['id' => $this->id]);
        }
    }

    public function savePassword(): bool {

        if(!isset($this->id)) return false;

        $data = [
            'password' => $this->password,
        ];

        return (bool)Connection::doUpdate(ORION_DB, self::$table, $data, ['id' => $this->id]);
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

    public function adquireGame(Game|int $game, $checkoutID, &$errorReason = ""): bool {
        if($this->hasAdquiredGame($game)){
            $errorReason = "Juego ya adquirido";
            return false;
        }
        $gameId = $game instanceof Game ? $game->id : $game;
        
        if(!Connection::doInsert(ORION_DB, "owns", ["game_id" => $gameId, "user_id" => $this->id, "checkout_id" => $checkoutID])) {
            $errorReason = "Fallo en la inserción en la base de datos";
            return false;
        }
        return true;
    }

    public function hasAdquiredGame(Game|int $game, $addDev = true, &$checkoutID = null): bool {
        if(!($game instanceof Game)){
            $game = Game::getById($game);
        }

        if($game->getDeveloper() == $this->getDeveloperInfo() && $addDev) return true;
        $select = Connection::doSelect(ORION_DB, "owns", ["game_id" => $game->id, "user_id" => $this->id]);
        
        if (count($select) === 1) {
            $checkoutID = $select[0]['checkout_id'];
            return true;
        }
        return false;
    }

    public function getAdquiredGames($addDev = true): array {
        if($addDev && !is_null($this->getDeveloperInfo())){
            $games = $this->getDeveloperInfo()->getGames();
        } else {
            $games = [];
        }

        $select = Connection::doSelect(ORION_DB, "owns", ["user_id" => $this->id]);
        
        foreach ($select as $gameRow) {
            $games[] = Game::getById($gameRow['game_id']);
        }

        return $games;
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

    // Relationship with Achievement
    public function unlockAchievement(Achievement|int $achievement): bool {
        $achievementId = $achievement instanceof Achievement ? $achievement->id : $achievement;
        if (!$this->hasUnlockedAchievement($achievementId)) {
            return Connection::doInsert(ORION_DB, 'unlocks', [
                "achievement_id" => $achievementId,
                "user_id" => $this->id
            ]);
        }
        return false;
    }

    public function hasUnlockedAchievement(Achievement|int $achievement, ?string &$dateUnlocked = null): bool {
        $achievementId = $achievement instanceof Achievement ? $achievement->id : $achievement;
        $select = Connection::doSelect(ORION_DB, "unlocks", ["achievement_id" => $achievementId, "user_id" => $this->id]);

        if (count($select) === 1) {
            $dateUnlocked = $select[0]['date'];
            return true;
        }
        return false;
    }

    public function getUnlockedAchievements(): array {
        $achievements = [];
        $select = Connection::doSelect(ORION_DB, "unlocks", ["user_id" => $this->id]);

        foreach ($select as $achievementRow) {
            $achievements[] = Achievement::getById($achievementRow['achievement_id']);
        }
        return $achievements;
    }

    public function getUnlockedAchievementDate(Achievement|int $achievement): ?string {
        $achievementId = $achievement instanceof Achievement ? $achievement->id : $achievement;
        $select = Connection::doSelect(ORION_DB, "unlocks", ["achievement_id" => $achievementId, "user_id" => $this->id]);

        return count($select) === 1 ? $select[0]['date'] : null;
    }

    // Relationship with Stat
    public function updateStat(Stat|int $stat, int $value): bool {
        $statId = $stat instanceof Stat ? $stat->id : $stat;
        $currentValue = $this->getStatValue($statId);

        if ($stat instanceof Stat && $stat->type === ESTAT_TYPE::BEST) {
            if ($currentValue !== null && $value <= $currentValue) {
                return false;
            }
        }

        if ($this->hasStat($statId)) {
            return (bool)Connection::doUpdate(ORION_DB, 'has_stat', ['value' => $value], ['user_id' => $this->id, 'stat_id' => $statId]);
        } else {
            return (bool)Connection::doInsert(ORION_DB, 'has_stat', ['user_id' => $this->id, 'stat_id' => $statId, 'value' => $value]);
        }
    }

    public function hasStat(Stat|int $stat): bool {
        $statId = $stat instanceof Stat ? $stat->id : $stat;
        $select = Connection::doSelect(ORION_DB, "has_stat", ["stat_id" => $statId, "user_id" => $this->id]);

        return count($select) === 1;
    }

    public function getStatValue(Stat|int $stat): ?int {
        $statId = $stat instanceof Stat ? $stat->id : $stat;
        $select = Connection::doSelect(ORION_DB, "has_stat", ["stat_id" => $statId, "user_id" => $this->id]);

        return count($select) === 1 ? $select[0]['value'] : null;
    }
    
    // Auth related
    public function toSessionArray(): array {
        return isset($this->id) ? [
            "id" => $this->id,
            "username" => $this->username,
            "profile_pic" => $this->profile_pic
        ] : [];
    }

    public static function getCount(){
        $count = Connection::customQuery(ORION_DB, "SELECT COUNT(id) FROM ".self::$table)->fetch(PDO::FETCH_BOTH);

        return $count[0];
    }
}

<?php

class User {

    private static $db;
    private static $table = 'users';

    //Basic user info
    public $id;
    public $username;
    public $password;
    public $role;

    //Community profile
    public $profile_pic;
    public $motd;
    public $badge;

    private static function loadDB() {
        if(!isset($db)){
            $db = ORION_DB;
        }
    }

    public function __construct($username, $password, $role, $profile_pic = null, $motd = null, $badge = null) {
        
        self::loadDB();

        $this->username = $username;
        $this->password = $password;
        $this->role = $role;
        $this->profile_pic = $profile_pic;
        $this->motd = $motd;
        $this->badge = $badge;
    }

    public function save() {

        $data = [
            'username' => $this->username,
            'password' => password_hash($this->password, PASSWORD_BCRYPT),  // Hasheando la contraseña
            'role' => $this->role,
            'profile_pic' => $this->profile_pic,
            'motd' => $this->motd,
            'badge' => $this->badge,
        ];

        if(!isset($this->id) || !self::getById($this->id)){
            $data['created_at'] = date('Y-m-d H:i:s');
            return Connection::doInsert(self::$db, self::$table, $data);
        } else {
            return Connection::doUpdate(self::$db, self::$table, $data, ['id' => $this->id]);
        }
    }

    public static function getById($id) {
        self::loadDB();

        $user = Connection::doSelect(self::$db, self::$table, [
            "id" => $id
        ]);

        return $user;
    }

    public function delete() {
        $conditions = ['id' => $id];
        return Connection::doDelete(self::$db, self::$table, $conditions);
    }
}

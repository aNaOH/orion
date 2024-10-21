<?php

class User {

    private $dbConn;
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

    public function __construct($username, $password, $role, $profile_pic = null, $motd = null, $badge = null) {
        global $dbConn;

        $this->dbConn = $dbConn;

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
            return Connection::doInsert($this->db, self::$table, $data);
        } else {
            return Connection::doUpdate($this->db, self::$table, $data, ['id' => $this->id]);
        }
    }

    public static function getById($id) : User {
        $user = Connection::doSelect($dbConn, self::$table, [
            "id" => $this->$id
        ]);

        return $user;
    }

    public function delete() {
        $conditions = ['id' => $id];
        return Connection::doDelete($this->db, self::$table, $conditions);
    }
}

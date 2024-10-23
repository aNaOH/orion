<?php

class User {

    private static $table = 'users';

    //Basic user info
    public $id;
    public $email;
    public $username;
    public $password;
    public $role;

    //Community profile
    public $profile_pic;
    public $motd;
    public $badge;

    public function __construct($email, $username, $password, $role, $profile_pic = null, $motd = null, $badge = null, $id = null) {

        $this->email = $email;
        $this->username = $username;
        $this->password = $password;
        $this->role = ($role::class == EUSER_TYPE::class) ? $role : EUSER_TYPE::from($role);
        $this->profile_pic = $profile_pic;
        $this->motd = $motd;
        $this->badge = $badge;

        $this->id = $id;
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
            return Connection::doInsert(ORION_DB, self::$table, $data);
        } else {
            return Connection::doUpdate(ORION_DB, self::$table, $data, ['id' => $this->id]);
        }
    }

    public static function getById($id) : User|null {

        $user = Connection::doSelect(ORION_DB, self::$table, [
            "id" => $id
        ]);

        if(count($user) == 1){
            return new User(
                $user['email'], 
                $user['username'], 
                $user['password'], 
                $user['role'], 
                $user['profile_pic'], 
                $user['motd'], 
                $user['badge'], 
                $user['id']
            );
        }

        return null;
    }

    public static function getByEmail($email) : User|null {

        $user = Connection::doSelect(ORION_DB, self::$table, [
            "email" => $email
        ]);

        if(count($user) == 1){
            return new User(
                $user['email'], 
                $user['username'], 
                $user['password'], 
                $user['role'], 
                $user['profile_pic'], 
                $user['motd'], 
                $user['badge'], 
                $user['id']
            );
        }

        return null;
    }

    public function delete() {
        $conditions = ['id' => $this->id];
        return Connection::doDelete(ORION_DB, self::$table, $conditions);
    }

    public function toSessionArray(){
        return [
            "id" => $this->id,
            "username" => $this->username,
            "profile_pic" => $this->profile_pic
        ];
    }
}

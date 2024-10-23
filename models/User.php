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

    public function __construct($email, $username, $password, EUSER_TYPE|int $role, $profile_pic = null, $motd = null, $badge = null, $id = null) {

        $parsedRole = 0;

        if(is_numeric($role)){
            $parsedRole == EUSER_TYPE::from($role);
        } else{
            $parsedRole = $role;
        }

        $this->email = $email;
        $this->username = $username;
        $this->password = $password;
        $this->role = $parsedRole;
        $this->profile_pic = $profile_pic;
        $this->motd = $motd;
        $this->badge = $badge;

        $this->id = $id;
    }

    public function save() {

        $data = [
            'email' => $this->email,
            'username' => $this->username,
            'password' => password_hash($this->password, PASSWORD_BCRYPT),  // Hasheando la contraseña
            'role' => $this->role->value,
            'profile_pic' => $this->profile_pic,
            'motd' => $this->motd,
            'badge_id' => $this->badge,
        ];

        if(!isset($this->id) || !self::getById($this->id)){
            $result = Connection::doInsert(ORION_DB, self::$table, $data);
            $id = count(Connection::doSelect(ORION_DB, self::$table));
            $this->id = $id;
            return $result;
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

    public static function getByEmail($email) : User|null {

        $user = Connection::doSelect(ORION_DB, self::$table, [
            "email" => $email
        ]);

        if(count($user) == 1){
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

    public function getProfilePicURL() {
        return "/media/profile/".($user->profile_pic ?? "default");
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

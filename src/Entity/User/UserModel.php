<?php namespace Holamanola45\Www\Entity\User;

use DateTime;

class UserModel {
    function __set($key, $value) {
        $this->$key = $value;
    }

    public int $id;

    public string $username;

    public string $password;

    public string $created_at;

    public string $created_by_ip;

    public $last_login_ip;

    public $activate_at;

    public string $email;

    public string $token;
}
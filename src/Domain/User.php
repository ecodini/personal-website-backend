<?php namespace Holamanola45\Www\Domain;

use DateTime;

class User {
    function __construct($array) {
        foreach($array as $key=>$value) {
            $this->$key = $value;
        }
    }

    public int $id;

    public string $username;

    public string $password;

    public string $created_at;

    public string $created_by_ip;

    public $last_login_ip;

    public $activate_at;

    public $extra_properties;
}
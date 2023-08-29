<?php namespace Holamanola45\Www\Lib\Auth;

class PasswordCrypt {
    public static function encrypt($string): string {
        return password_hash($string, PASSWORD_BCRYPT);
    }

    public static function verify($string, $hash): bool {
        return password_verify($string, $hash); 
    }
}
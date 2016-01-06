<?php
namespace FileSharing\Helper;

class Token
{
    public static $token;

    public static function init($expire = 604800)
    {
        if (!self::issetToken()) {
            $token = self::generateToken();
        } else {
            $token = self::getToken();
        }
        $time = time() + $expire;
        self::setToken($token, $time);
        self::$token = $token;
    }

    public static function generateToken()
    {
        $salt = HashGenerator::generateSalt();
        return HashGenerator::generateHash($salt, $salt);
    }

    public static function getToken()
    {
        return (self::issetToken()) ? $_COOKIE['csrf_token'] : false;
    }
    public static function setToken($token, $time)
    {
        setcookie('csrf_token', $token, $time);
    }

    public static function issetToken() {
        if (!isset($_COOKIE['csrf_token'])) {
            return false;
        }
        return true;
    }

    public static function checkToken()
    {
        if (!isset($_POST['logoutForm']['csrf_token'])) return false;
        $postToken = $_POST['logoutForm']['csrf_token'];
        $cookieToken = self::getToken();
        if ($postToken !== $cookieToken) return false;
        return true;
    }
}

<?php

namespace Storage\Auth;

use Storage\Mapper\UserMapper;

class LoginManager
{
    public static function login()
    {
        session_start();
        if (!isset($_SESSION['id']) or !isset($_SESSION['hash']))
            return false;
        $id = $_SESSION['id'];
        $hash = $_SESSION['hash'];
        setcookie('id', $id, time() + 3600 * 24 * 7);
        setcookie('hash', $hash, time() + 3600 * 24 * 7);
        $_COOKIE['id'] = $id;
        $_COOKIE['hash'] = $hash;
        session_destroy();
        return true;
    }

    public static function logout()
    {
        setcookie('id', '');
        setcookie('hash', '');
    }

    public static function isLoggedIn()
    {
        if (!isset($_COOKIE['id']) or !isset($_COOKIE['hash'])) return false;
        $id = intval($_COOKIE['id']);
        $hash = strval($_COOKIE['hash']);
        $db_config = parse_ini_file(\BASE_DIR.'/config.ini');
        $connection = new \PDO(
                    $db_config['conn'],
                    $db_config['user'],
                    $db_config['pass']
                );
        $mapper = new UserMapper($connection);
        if (!$user = $mapper->findById($id)) return false;
        if ($user->hash != $hash) return false;

        return true;
    }

    public static function setSession(array $params) // не хватает фантазии дать внятное имя
    {
        session_start();
        foreach ($params as $key=>$value) {
            $_SESSION[$key] = $value;
        }
    }
}
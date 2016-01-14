<?php
namespace FileSharing\Helper;

use Slim\Http\Request;
use Slim\Http\Response;

class Token
{
    public static $token;

    private static $response;
    private static $request;

    public static function init(
        Response $response,
        Request $request,
        $expire = 604800
    ) {
        self::$response = $response;
        self::$request = $request;
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
        return self::$request->cookies->get('csrf_token');
    }
    public static function setToken($token, $time)
    {
        self::$response->setCookie(
            'csrf_token',
            ['value' => $token, 'path' => '/', 'expires' => $time]
        );
    }

    public static function issetToken() {
        if (self::$request->cookies->get('csrf_token')) {
            return true;
        }
        return false;
    }
}

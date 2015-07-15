<?php namespace Storage\Helper;

class HashGenerator
{
    const LENGTH = 10;
    
    public static function getCharacters()
    {
        return '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    }

    public static function generateSalt()
    {
        $salt = '';
        $characters = self::getCharacters();
        $charactersLength = strlen($characters);
        for ($i = 0; $i < self::LENGTH; $i++) {
            $salt .= $characters[rand(0, $charactersLength - 1)];
        }
        return $salt;
    }

    public static function generateHash($saltedPassword)
    {
        return sha1($saltedPassword);
    }
}
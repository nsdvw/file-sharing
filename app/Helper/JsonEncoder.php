<?php
namespace FileSharing\Helper;

class JsonEncoder
{
    public static function createResponse($text, $error = null)
    {
        return self::encode(["text" => $text, "error" => $error]);
    }

    public static function encode($item)
    {
        return json_encode($item);
    }

    public static function decode($item)
    {
        return json_decode($item);
    }
}

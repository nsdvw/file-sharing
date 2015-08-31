<?php namespace Storage\Model;

class MediaInfo
{
    public $resolution_x;
    public $resolution_y;
    public $frame_rate;
    public $encoding;
    public $playtime;
    public $bits_per_sample;

    public static function fromFile($fileName)
    {
        $mediaInfo = new self;
        $id3 = new \getID3();
        $id3->encoding = 'UTF-8';
        $finfo = $id3->analyze($fileName);
        if (isset($finfo['video']['resolution_x'])) {
            $mediaInfo->resolution_x = $finfo['video']['resolution_x'];
        }
        if (isset($finfo['video']['resolution_y'])) {
            $mediaInfo->resolution_y = $finfo['video']['resolution_y'];
        }
        if (isset($finfo['video']['frame_rate'])) {
            $mediaInfo->frame_rate = $finfo['video']['frame_rate'];
        }
        if (isset($finfo['encoding'])) {
            $mediaInfo->encoding = $finfo['encoding'];
        }
        if (isset($finfo['playtime_string'])) {
            $mediaInfo->playtime = $finfo['playtime_string'];
        }
        if (isset($finfo['video']['bits_per_sample'])) {
            $mediaInfo->bits_per_sample = $finfo['video']['bits_per_sample'];
        }
        return $mediaInfo;
    }

    public static function fromDataBase(\stdClass $obj)
    {
        $mediaInfo = new self;
        foreach ($obj as $property => $value) {
            $mediaInfo->$property = $obj->$property;
        }
        return $mediaInfo;
    }
}
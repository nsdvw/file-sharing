<?php
namespace Storage\Model;

use Storage\Helper\ViewHelper;

class File
{
    public $id;
    public $name;
    public $author_id;
    public $size;
    public $mime_type;
    public $mediaInfo;
    public $upload_time;
    public $download_counter;

    private function getPublicFields()
    {
        return [
            'id',
            'name',
            'author_id',
            'size',
            'mime_type',
            'mediaInfo',
            'upload_time',
            'download_counter',
        ];
    }

    public static $videoTypes = [
        'webmv'=>'video/webm',
        'm4v'=>'video/mp4',
        'ogv'=>'video/ogg',
        'flv'=>'video/x-flv',
    ];

    public static $audioTypes = [
        'mp3'=>'audio/mpeg',
        'm4a'=>'audio/mp4',
        'wav'=>'audio/x-wav',
        'fla'=>'audio/x-flv',
        'webma'=>'audio/webm',
        'oga'=>'audio/ogg',
    ];

    public static function jPlayerTypes()
    {
        return array_merge(self::$audioTypes, self::$videoTypes);
    }

    public static function jPlayerMediaType($fileType)
    {
        return array_search( $fileType, self::jPlayerTypes() );
    }

    public function isImage()
    {
        $types = ['image/jpeg', 'image/png', 'image/gif'];
        return in_array($this->mime_type, $types);
    }

    public function isVideo()
    {
        return in_array($this->mime_type, self::$videoTypes);
    }

    public function isAudio()
    {
        return in_array($this->mime_type, self::$audioTypes);
    }

    public function isText()
    {
        $types = [
            'text/plain',
            'application/xml',
            'text/rtf',
            'text/php',
            'text/html',
            'text/x-php',
        ];
        return in_array($this->mime_type, $types);
    }

    public function isArchive()
    {
        $types = [
            'application/zip',
            'application/gzip',
            'application/x-gzip',
        ];
        return in_array($this->mime_type, $types);
    }

    public function toArray()
    {
        foreach ($this as $propertyName => $propertyValue) {
            if ($propertyValue instanceof MediaInfo) {
                foreach ($propertyValue as $key => $value) {
                    $mediaInfo[$key] = $value;
                }
                $array[$propertyName] = $mediaInfo;
            } else {
                if ($propertyName === 'size')
                    $propertyValue = ViewHelper::formatSize($propertyValue);
                if ( in_array($propertyName, $this->getPublicFields()) )
                    $array[$propertyName] = $propertyValue;
            }
        }
        return $array;
    }
}

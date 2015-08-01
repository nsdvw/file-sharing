<?php namespace Storage\Model;

class File
{
    public $id;
    public $name;
    public $author_id;
    public $size;
    public $mime_type;
    public $mediaInfo;
    public static $videoTypes = array(
            'webmv'=>'video/webm',
            'm4v'=>'video/mp4',
            'ogv'=>'video/ogg',
            'flv'=>'video/x-flv',
        );
    public static $audioTypes = array(
            'mp3'=>'audio/mpeg',
            'm4a'=>'audio/mp4',
            'wav'=>'audio/x-wav',
            'fla'=>'audio/x-flv',
            'webma'=>'audio/webm',
            'oga'=>'audio/ogg',
        );

    public static function fromUser(
        $name,
        $tmp_name,
        $author_id = null
    ) {
        $file = new self;
        $file->name = $name;
        $file->author_id = $author_id;
        $file->size = filesize($tmp_name);
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $file->mime_type = $finfo->file($tmp_name);
        $file->mediaInfo = MediaInfo::fromUser($tmp_name);
        return $file;
    }

    public function isImage()
    {
        $types = array('image/jpeg', 'image/png', 'image/gif');
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
        $types = array(
            'text/plain',
            'application/xml',
            'text/rtf',
            'text/php',
            'text/html',
            'text/x-php',
        );
        return in_array($this->mime_type, $types);
    }

    public function isArchive()
    {
        $types = array(
            'application/zip',
            'application/gzip',
            'application/x-gzip',
        );
        return in_array($this->mime_type, $types);
    }
}

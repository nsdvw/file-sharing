<?php namespace Storage\Model;

class File
{
    public $id;
    public $name;
    public $description;
    public $author_id;
    public $size;
    public $mime_type;
    public $mediaInfo;

    public static function fromUser(
        $name,
        $tmp_name,
        $description = null,
        $author_id = null
    ) {
        $file = new self;
        $file->name = $name;
        $file->description = $description;
        $file->author_id = $author_id;
        $file->size = filesize($tmp_name);
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $file->mime_type = $finfo->file($tmp_name);
        $file->mediaInfo = \Storage\Model\MediaInfo::fromUser($tmp_name);
        return $file;
    }

    public function isImage()
    {
        $types = array('image/jpeg', 'image/png', 'image/gif');
        return in_array($this->mime_type, $types);
    }

    public function isVideo()
    {
        $types = array('video/webm', 'video/mp4', 'video/ogg');
        return in_array($this->mime_type, $types);
    }
}

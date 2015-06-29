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

    static public function fromUser(
        $name,
        $tmp_name,
        $description = NULL,
        $author_id = NULL
        )
    {
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

    static public function formatSize($size)
    {
        if ($size > pow(1024, 3)) {
            $size = round($size / pow(1024, 3), 2) . ' Гб';
        } elseif ($size > pow(1024, 2)) {
            $size = round($size / pow(1024, 2), 2) . ' Мб';
        } elseif ($size > 1024) {
            $size = round($size / 1024, 2) . ' Кб';
        } else {
            return "$size байт";
        }
        return $size;
    }
}
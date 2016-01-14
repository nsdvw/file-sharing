<?php
namespace FileSharing\Model;

use FileSharing\Helper\ViewHelper;

class File
{
    public $id;
    public $name;
    public $author_id;
    public $author_token;
    public $size;
    public $mime_type;
    public $mediaInfo;
    public $upload_time;
    public $best_before;
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

    private function getVideoTypes()
    {
        return [
            'webmv'=>'video/webm',
            'm4v'=>'video/mp4',
            'ogv'=>'video/ogg',
            'flv'=>'video/x-flv',
        ];
    }

    private function getAudioTypes()
    {
        return [
            'mp3'=>'audio/mpeg',
            'm4a'=>'audio/mp4',
            'wav'=>'audio/x-wav',
            'fla'=>'audio/x-flv',
            'webma'=>'audio/webm',
            'oga'=>'audio/ogg',
        ];
    }

    private function jPlayerTypes()
    {
        return array_merge( $this->getAudioTypes(), $this->getVideoTypes() );
    }

    public function jPlayerMediaType($fileType)
    {
        return array_search( $fileType, $this->jPlayerTypes() );
    }

    public function isImage()
    {
        $types = ['image/jpeg', 'image/png', 'image/gif'];
        return in_array($this->mime_type, $types);
    }

    public function isVideo()
    {
        return in_array($this->mime_type, $this->getVideoTypes());
    }

    public function isAudio()
    {
        return in_array($this->mime_type, $this->getAudioTypes());
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
}

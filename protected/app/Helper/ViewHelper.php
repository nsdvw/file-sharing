<?php namespace Storage\Helper;

class ViewHelper
{
    static public function getUploadName($id, $name)
    {
        return "{$id}_{$name}.txt";
    }

    static public function getPreviewName($id)
    {
        return "{$id}.txt";
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

    static function getDownloadUrl($id, $name)
    {
        return \DOWNLOAD_DIR . \DIRECTORY_SEPARATOR . $id .
                                \DIRECTORY_SEPARATOR . $name;
    }

    static function getUploadPath($id, $name)
    {
        return \UPLOAD_DIR.\DIRECTORY_SEPARATOR.self::getUploadName($id, $name);
    }

    static function getPreviewPath($id)
    {
        return \PREVIEW_DIR.\DIRECTORY_SEPARATOR.self::getPreviewName($id);
    }
}
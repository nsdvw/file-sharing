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
}
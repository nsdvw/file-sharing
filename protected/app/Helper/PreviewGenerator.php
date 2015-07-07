<?php namespace Storage\Helper;

class PreviewGenerator
{
    static public function hasPreview($file)
    {
        if (file_exists($file)) {
            return true;
        } else {
            return false;
        }
    }

    static public function createPreview(\Storage\Model\File $file,
                                        $preview_width = 300)
    {
        $preview = fopen(ViewHelper::getPreviewPath($file->id), 'w');
        fclose($preview);
        $original_width = $file->mediaInfo->resolution_x;
        $original_height = $file->mediaInfo->resolution_y;
        $ratio = $original_width / $preview_width;
        $preview_height = round($original_height / $ratio);
        $content = imagecreatetruecolor($preview_width, $preview_height);

        $uploadedFile = ViewHelper::getUploadPath($file->id, $file->name);
        switch ($file->mime_type) {
        case 'image/jpeg':
            $fullSize = imagecreatefromjpeg($uploadedFile);
            imagecopyresampled($content, $fullSize, 0, 0, 0, 0,
            $preview_width, $preview_height, $original_width, $original_height);
            imagejpeg($content, ViewHelper::getPreviewPath($file->id));
            break;
        case 'image/gif':
            $fullSize = imagecreatefromgif($uploadedFile);
            imagecopyresampled($content, $fullSize, 0, 0, 0, 0,
            $preview_width, $preview_height, $original_width, $original_height);
            imagegif($content, ViewHelper::getPreviewPath($file->id));
            break;
        case 'image/png':
            $fullSize = imagecreatefrompng($uploadedFile);
            imagecopyresampled($content, $fullSize, 0, 0, 0, 0,
            $preview_width, $preview_height, $original_width, $original_height);
            imagepng($content, ViewHelper::getPreviewPath($file->id));
            break;
        }
    }
}
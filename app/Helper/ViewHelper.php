<?php
namespace FileSharing\Helper;

use FileSharing\Model\File;

class ViewHelper
{
    public function getUploadName($id, $name)
    {
        return "{$id}_{$name}.txt";
    }

    public function getPreviewName($id)
    {
        return "{$id}.txt";
    }

    public function formatSize($size)
    {
        if ($size > pow(1024, 3)) {
            $size = round($size / pow(1024, 3), 2) . ' Gb';
        } elseif ($size > pow(1024, 2)) {
            $size = round($size / pow(1024, 2), 2) . ' Mb';
        } elseif ($size > 1024) {
            $size = round($size / 1024, 2) . ' Kb';
        } else {
            return "$size bytes";
        }
        return $size;
    }

    public function getDownloadUrl($id, $name)
    {
        return "/download/{$id}/{$name}";
    }

    public function getUploadPath($id, $name)
    {
        return 'upload/' . $this->getUploadName($id, $name);
    }

    public function getUploadUrl($id, $name)
    {
        return '/' . $this->getUploadPath($id, $name);
    }

    public function getPreviewPath($id)
    {
        return 'preview/' . $this->getPreviewName($id);
    }

    public function getPreviewUrl($id)
    {
        return '/' . $this->getPreviewPath($id);
    }

    public function getDetailViewUrl($id)
    {
        return "/view/{$id}";
    }

    public function getPagerLink($number)
    {
        return "/view?page={$number}";
    }

    public function createPreviewChecker(File $file)
    {
        if ($file->isImage()) {
            $path = $this->getPreviewPath($file->id);
            if (!PreviewGenerator::hasPreview($path)) {
                PreviewGenerator::createPreview($file);
            }
        }
    }
}

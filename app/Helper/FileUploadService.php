<?php
namespace Storage\Helper;

use Storage\Model\File;
use Storage\Mapper\FileMapper;

class FileUploadService
{
    protected $mapper;

    public function __construct(FileMapper $mapper)
    {
        $this->mapper = $mapper;
    }

    public function upload(File $file, $tempName)
    {
        $this->mapper->beginTransaction();
        $this->mapper->save($file);
        if (move_uploaded_file(
            $tempName,
            ViewHelper::getUploadPath($file->id, $file->name)
        )) {
            $this->mapper->commit();
            return true;
        } else {
            $this->mapper->rollBack();
            return false;
        }
    }
}

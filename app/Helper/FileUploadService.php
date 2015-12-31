<?php
namespace Storage\Helper;

use Storage\Model\File;
use Storage\Mapper\FileMapper;
use Storage\Form\UploadForm;

class FileUploadService
{
    private $mapper;

    public function __construct(FileMapper $mapper)
    {
        $this->mapper = $mapper;
    }

    public function upload(UploadForm $form)
    {
        $this->mapper->beginTransaction();
        $file = $form->getFile();
        $this->mapper->save($file);
        if (move_uploaded_file(
            $form->getTempFileName(),
            ViewHelper::getUploadPath($file->id, $file->name)
        )) {
            $this->mapper->commit();
            return true;
        } else {
            $this->mapper->rollBack();
            $form->setErrorMessage(UploadForm::SERVER_ERROR);
            return false;
        }
    }
}

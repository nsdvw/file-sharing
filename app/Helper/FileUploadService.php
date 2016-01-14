<?php
namespace FileSharing\Helper;

use FileSharing\Model\File;
use FileSharing\Mapper\FileMapper;
use FileSharing\Form\UploadForm;

class FileUploadService
{
    private $mapper;
    private $viewHelper;

    public function __construct(FileMapper $mapper)
    {
        $this->mapper = $mapper;
        $this->viewHelper = new ViewHelper;
    }

    public function upload(UploadForm $form)
    {
        $this->mapper->beginTransaction();
        $file = $form->getFile();
        $this->mapper->save($file);
        if (move_uploaded_file(
            $form->getTempFileName(),
            $this->viewHelper->getUploadPath($file->id, $file->name)
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

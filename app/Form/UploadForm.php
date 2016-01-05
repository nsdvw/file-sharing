<?php
namespace FileSharing\Form;

use FileSharing\Model\File;
use FileSharing\Model\MediaInfo;
use Slim\Http\Request;

class UploadForm extends AbstractForm
{
    const AGREE_ERROR = 'You must agree with TOS';
    const EMPTY_ERROR = 'You didn\'t choose the file';
    const SERVER_ERROR = 'File wasn\'t uploaded, please try again later';

    public $fileName;
    public $agree;
    public $author_id;

    private $tempName;
    private $error;
    private $file;

    public function __construct(Request $request, array $files, $author_id=null)
    {
        $this->author_id = $author_id;
        $postData = $request->post('upload');
        $this->agree = isset($postData['agree']) ? $postData['agree'] : null;
        $this->error = isset($_FILES['upload']['error']['file1'])
                 ? $_FILES['upload']['error']['file1'] : null;
        $this->fileName = isset($_FILES['upload']['name']['file1'])
                          ? $_FILES['upload']['name']['file1'] : null;
        $this->tempName = isset($_FILES['upload']['tmp_name']['file1'])
                          ? $_FILES['upload']['tmp_name']['file1'] : null;
        $this->setFile();
    }

    public function validate()
    {
        if (!$this->agree) {
            $this->errorMessage = self::AGREE_ERROR;
            return false;
        } elseif (!$this->fileName) {
            $this->errorMessage = self::EMPTY_ERROR;
            return false;
        } elseif ($this->error) {
            $this->errorMessage = self::SERVER_ERROR;
        }
        return true;
    }

    public function getTempFileName()
    {
        return $this->tempName;
    }

    public function getFile()
    {
        return $this->file;
    }

    public function setErrorMessage($errorMessage)
    {
        $this->errorMessage = $errorMessage;
    }

    private function setFile()
    {
        $file = new File;
        $this->file = $file;
        if ($this->tempName == null) return;
        $file->name = $this->fileName;
        $file->author_id = $this->author_id;
        $file->size = filesize($this->tempName);
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $file->mime_type = $finfo->file($this->tempName);
        $file->mediaInfo = MediaInfo::fromFile($this->tempName);
    }
}

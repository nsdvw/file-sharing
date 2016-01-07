<?php
namespace FileSharing\Form;

use Slim\Http\Request;
use FileSharing\Model\File;

class EditFileForm extends AbstractForm
{
    private $file;

    public function __construct(Request $request, $fileID)
    {
        $postData = $request->post('edit');
        $this->file = new File;
        $this->file->id = $fileID;
        $this->file->description = isset($postData['description'])
            ? $postData['description'] : null;
        $this->file->best_before = isset($postData['expiration'])
            ? $postData['expiration'] : null;
    }

    public function getFile()
    {
        return $this->file;
    }
}

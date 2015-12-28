<?php
namespace Storage\Model;

class FormWithCaptcha extends Form
{
    public $captcha;
    protected $fields = ['captcha'];

    public function rules()
    {
        return [
            'captcha' => ['notEmpty'=>true, 'captcha'=>true],
        ];
    }

    public function validateCaptcha($field)
    {
        session_start();
        if ($this->$field != $_SESSION['captcha']) {
            $this->errorMessage = 'Wrong captcha';
            return false;
        }
        return true;
    }
}

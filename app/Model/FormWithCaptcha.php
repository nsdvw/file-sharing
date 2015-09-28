<?php

namespace Storage\Model;

class FormWithCaptcha extends Form
{
    public $captcha;
    protected $fields = array('captcha');

    public function rules()
    {
        return array(
            'captcha' => array('notEmpty'=>true, 'captcha'=>true),
        );
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

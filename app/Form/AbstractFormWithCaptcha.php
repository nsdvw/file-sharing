<?php
namespace Storage\Form;

abstract class AbstractFormWithCaptcha extends AbstractForm
{
    protected $captchaRequired = false;

    public function setCaptchaRequired($boolean = true)
    {
        $this->captchaRequired = $boolean;
    }

    protected function validateCaptcha($field)
    {
        session_start();
        if (!$this->captchaRequired) {
            return true;
        }
        if ($this->$field != $_SESSION['captcha']) {
            $this->errorMessage = 'Wrong captcha';
            return false;
        }
        return true;
    }
}

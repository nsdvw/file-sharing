<?php namespace Storage\Model;

class Form
{
    public $errorMessage;

    public function __construct(array $fields)
    {
        foreach ($fields as $field=>$value) {
            $this->$field = trim($value);
        }
    }

    public function validate()
    {
        $rules = $this->rules();
        foreach ($rules as $field=>$list) {
            foreach ($list as $rule=>$attributes) {
                $rule = 'validate' . ucfirst($rule);
                if ( !$this->$rule($field, $attributes) ) return false;
            }
        }
        return true;
    }

    public function validateNotEmpty($field, $flag = true)
    {
        if (empty($this->$field)) {
            $this->errorMessage = "$field can't be empty";
            return false;
        }
        return true;
    }

    public function validateMaxLength($field, $maxLength)
    {
        if (mb_strlen($this->$field) > $maxLength) {
            $this->errorMessage = "$field must be maximum $maxLength symbols";
            return false;
        }
        return true;
    }

    public function validateMinLength($field, $minLength)
    {
        if (mb_strlen($this->$field) < $minLength) {
            $this->errorMessage = "$field must be minimum $minLength symbols";
            return false;
        }
        return true;
    }

    public function validateIsEmail($field, $flag = true)
    {
        $regExp = '/^[^@\s]+@[^@\s]+\.[^@\s]+$/ui';
        if (!preg_match($regExp, $this->$field)) {
            $this->errorMessage = "Incorrect email";
            return false;
        }
        return true;
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

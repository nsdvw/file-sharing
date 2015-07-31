<?php namespace Storage\Model;

class Form
{
    public $errorMessage;

    public function __construct(array $fields)
    {
        foreach ($fields as $field=>$value) {
            $this->$field = $value;
        }
    }

    public function validate()
    {
        $rules = $this->rules();
        foreach ($rules as $field=>$list) {
            foreach ($list as $rule=>$attributes) {
                if ( !$this->$rule($field, $attributes) ) return false;
            }
        }
        return true;
    }

    public function notEmpty($field, $flag = true)
    {
        if (empty($this->$field)) {
            $this->errorMessage = "$field can't be empty";
            return false;
        }
        return true;
    }

    public function maxLength($field, $maxLength)
    {
        mb_internal_encoding('UTF-8');
        if (mb_strlen($this->$field) > $maxLength) {
            $this->errorMessage = "$field must be maximum $maxLength symbols";
            return false;
        }
        return true;
    }

    public function minLength($field, $minLength)
    {
        mb_internal_encoding('UTF-8');
        if (mb_strlen($this->$field) < $minLength) {
            $this->errorMessage = "$field must be minimum $minLength symbols";
            return false;
        }
        return true;
    }

    public function isEmail($field, $flag = true)
    {
        $regExp = '/^[^@\s]+@[^@\s]+\.[^@\s]+$/ui';
        if (!preg_match($regExp, $this->$field)) {
            $this->errorMessage = "Incorrect email";
            return false;
        }
        return true;
    }
}

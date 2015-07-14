<?php namespace Storage\Model;

class Form
{
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
            foreach ($list as $validator=>$attributes) {
                if ( !$this->$validator($field, $attributes) ) return false;
            }
        }
        return true;
    }

    protected function notEmpty($field, $flag = true)
    {
        if (empty($this->$field)) {echo 'notEmptyERROR!!!';return false;}
        else {
            return true;
        }
    }

    protected function maxLength($field, $maxLength)
    {
        mb_internal_encoding('UTF-8');
        if (mb_strlen($this->$field) > $maxLength) {echo 'maxLengthERROR!!!';return false;}
        else {
            return true;
        }
    }

    protected function minLength($field, $minLength)
    {
        mb_internal_encoding('UTF-8');
        if (mb_strlen($this->$field) < $minLength) {echo 'minLengthERROR!!!';return false;}
        else return true;
    }

    protected function isEmail($field, $flag = true)
    {
        $regExp = '/^[^@\s]+@[^@\s]+\.[^@\s]+$/ui';
        if (!preg_match($regExp, $this->$field)) {echo 'isEmailERROR!!!';return false;}
        else return true;
    }
}
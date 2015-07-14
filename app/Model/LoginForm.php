<?php namespace Storage\Model;

class LoginForm extends Form
{
    public $email;
    public $password;

    public function rules()
    {
        return array(
            'email' => array('notEmpty'=>true, 'isEmail'=>true, 'maxLength'=>50),
            'password' => array('notEmpty'=>true, 'minLength'=>5, 'maxLength'=>50),
        );
    }
}
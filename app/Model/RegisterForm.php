<?php namespace Storage\Model;

class RegisterForm extends Form
{
    public $login;
    public $email;
    public $password;

    public function rules()
    {
        return array(
            'login'=>array('notEmpty'=>true, 'maxLength'=>20),
            'email' => array('notEmpty'=>true, 'isEmail'=>true, 'maxLength'=>50),
            'password' => array('notEmpty'=>true, 'minLength'=>5, 'maxLength'=>50),
        );
    }
}
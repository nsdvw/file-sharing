<?php namespace Storage\Model;

class LoginForm extends Form
{
    const WRONG_PASSWORD = 'wrong password';

    public $email;
    public $password;
    protected $fields = array('email', 'password');

    public function rules()
    {
        return array(
            'email' => array('notEmpty'=>true, 'isEmail'=>true, 'maxLength'=>50),
            'password' => array('notEmpty'=>true, 'minLength'=>5, 'maxLength'=>50),
        );
    }
}

<?php

namespace Storage\Model;

use Storage\Helper\HashGenerator;

class RegisterForm extends Form
{
    public $login;
    public $email;
    public $password;
    protected $fields = array('login', 'email', 'password');

    public function rules()
    {
        return array(
            'login' => array('notEmpty'=>true, 'maxLength'=>20, 'minLength'=>4),
            'email' => array('notEmpty'=>true, 'isEmail'=>true, 'maxLength'=>50),
            'password' => array('notEmpty'=>true, 'minLength'=>5, 'maxLength'=>50),
        );
    }

    public function getUser()
    {
        $user = new User;
        $user->login = $this->login;
        $user->email = $this->email;
        $user->salt = HashGenerator::generateSalt();
        $user->hash = HashGenerator::generateHash($user->salt, $this->password);
        return $user;
    }
}

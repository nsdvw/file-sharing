<?php
namespace Storage\Model;

class LoginForm extends Form
{
    const WRONG_PASSWORD = 'wrong password';

    public $email;
    public $password;
    protected $fields = ['email', 'password'];

    public function rules()
    {
        return [
            'email' =>
                ['notEmpty' => true, 'isEmail' => true, 'maxLength' => 50],
            'password' =>
                ['notEmpty' => true, 'minLength' => 5, 'maxLength' => 50],
        ];
    }
}

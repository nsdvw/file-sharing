<?php namespace Storage\Model;
use \Storage\Helper\HashGenerator;

class User
{
    public $id;
    public $login;
    public $email;
    public $salt;
    public $hash;
    public $registrationDate;

    public function fromForm(Form $form)
    {
        $this->login = $form->login;
        $this->email = $form->email;
        $this->salt = HashGenerator::generateSalt();
        $this->hash = HashGenerator::generateHash($this->salt . $form->password);
    }
}
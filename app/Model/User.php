<?php
namespace FileSharing\Model;

class User
{
    public $id;
    public $login;
    public $email;
    public $salt;
    public $hash;
    public $registrationDate;
}

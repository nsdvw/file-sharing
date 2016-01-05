<?php
namespace FileSharing\Auth;

use FileSharing\Mapper\UserMapper;
use FileSharing\Model\User;
use FileSharing\Helper\HashGenerator;
use FileSharing\Form\LoginForm;
use FileSharing\Form\RegisterForm;

class LoginManager
{
    private $mapper;

    public $loggedUser = null;

    public function __construct(UserMapper $mapper)
    {
        $this->mapper = $mapper;
        $this->loggedUser = $this->getLoggedUser();
    }

    public function logout()
    {
        setcookie('id', '');
        setcookie('hash', '');
    }

    public function getLoggedUser()
    {
        if (!isset($_COOKIE['id']) or !isset($_COOKIE['hash'])) {
            return null;
        }
        $id = intval($_COOKIE['id']);
        $hash = strval($_COOKIE['hash']);
        $user = $this->mapper->findById($id);
        if ($user->hash != $hash) return null;
        return $user;
    }

    public function validateLoginForm(LoginForm $form)
    {
        if (!$form->validate()) {
            return false;
        }
        $foundUser = $this->mapper->findByEmail($form->email);
        return $form->validatePassword($foundUser);
    }

    public function validateRegisterForm(RegisterForm $form)
    {
        if (!$form->validate()) {
            return false;
        }
        $foundUser = $this->mapper->findByEmail($form->email);
        return $form->validateUniqueEmail($foundUser);
    }

    public function authorizeUser(User $user)
    {
        setcookie('id', $user->id, time() + 3600*24*7);
        setcookie('hash', $user->hash, time() + 3600*24*7);
    }

    public function getUserID()
    {
        if ($this->isLogged()) {
            return $this->loggedUser->id;
        }
        return null;
    }

    public function isLogged()
    {
        if ($this->loggedUser !== null) {
            return true;
        }
        return false;
    }
}

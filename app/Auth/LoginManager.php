<?php
namespace FileSharing\Auth;

use FileSharing\Mapper\UserMapper;
use FileSharing\Model\User;
use FileSharing\Model\File;
use FileSharing\Helper\Token;
use FileSharing\Helper\HashGenerator;
use FileSharing\Form\LoginForm;
use FileSharing\Form\RegisterForm;

class LoginManager
{
    private $mapper;
    private $loggedUser = null;

    public $token;

    public function __construct(UserMapper $mapper)
    {
        $this->mapper = $mapper;
        $this->loggedUser = $this->getLoggedUser();
        $this->token = Token::$token;
    }

    public function logout()
    {
        setcookie('id', '');
        setcookie('hash', '');
        $this->loggedUser = null;
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
        if (!$form->validateUniqueEmail($foundUser)) {
            return false;
        }
        $foundUserID = $this->mapper->findByLogin($form->login);
        return $form->validateUniqueLogin($foundUserID);
    }

    public function authorizeUser(User $user, $remember = true)
    {
        $time = $remember ? time() + 3600*24*7 : 0;
        setcookie('id', $user->id, $time, '/');
        setcookie('hash', $user->hash, $time, '/');
        $this->loggedUser = $user;
    }

    public function getUserID()
    {
        if ($this->isLogged()) {
            return $this->loggedUser->id;
        }
        return null;
    }

    public function getUserLogin()
    {
        if ($this->isLogged()) {
            return $this->loggedUser->login;
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

    public function hasRights(File $file)
    {
        return ($this->isLogged() and $file->author_id == $this->getUserID())
            or ($this->token == $file->author_token);
    }

    public function checkToken($formData)
    {
        if (!isset($formData['csrf_token'])) {
            return false;
        }
        if ($this->token !== $formData['csrf_token']) {
            return false;
        }
        return true;
    }
}

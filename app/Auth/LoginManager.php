<?php
namespace Storage\Auth;

use Storage\Mapper\UserMapper;
use Storage\Model\User;
use Storage\Helper\HashGenerator;

class LoginManager
{
    protected $mapper;
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
        } else {
            $id = intval($_COOKIE['id']);
            $hash = strval($_COOKIE['hash']);
            $user = $this->mapper->findById($id);
            if ($user->hash != $hash) return null;
            return $user;
        }
    }

    public function validateUser($form)
    {
        $user = $this->mapper->findByEmail($form->email);
        if ($user === false) {
            return false;
        } elseif ($user->hash ===
            HashGenerator::generateHash($user->salt, $form->password)) {
            $this->loggedUser = $user;
            return true;
        } else {
            return false;
        }
    }

    public function authorizeUser(User $user = null)
    {
        if (!$user) {
            $user = $this->loggedUser;
        }
        setcookie('id', $user->id, time() + 3600 * 24 * 7);
        setcookie('hash', $user->hash, time() + 3600 * 24 * 7);
    }
}
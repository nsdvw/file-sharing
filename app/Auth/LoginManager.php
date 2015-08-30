<?php

namespace Storage\Auth;

use Storage\Mapper\UserMapper;
use Storage\Model\User;

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

    protected function getLoggedUser()
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

    public function authorizeUser(User $user)
    {
        setcookie('id', $user->id, time() + 3600 * 24 * 7);
        setcookie('hash', $user->hash, time() + 3600 * 24 * 7);
    }
}
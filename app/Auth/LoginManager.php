<?php
namespace FileSharing\Auth;

use FileSharing\Mapper\UserMapper;
use FileSharing\Model\User;
use FileSharing\Model\File;
use FileSharing\Helper\Token;
use FileSharing\Helper\HashGenerator;
use FileSharing\Form\LoginForm;
use FileSharing\Form\RegisterForm;
use Slim\Http\Response;
use Slim\Http\Request;

class LoginManager
{
    private $mapper;
    private $loggedUser = null;
    private $response;
    private $request;

    public $token;

    public function __construct(
        UserMapper $mapper,
        Response $response,
        Request $request
    ) {
        $this->mapper = $mapper;
        $this->response = $response;
        $this->request = $request;
        $this->loggedUser = $this->getLoggedUser();
        $this->token = Token::$token;
    }

    public function logout()
    {
        $this->response->deleteCookie('id');
        $this->response->deleteCookie('hash');
        $this->loggedUser = null;
    }

    public function getLoggedUser()
    {
        $id = intval($this->request->cookies->get('id'));
        $hash = intval($this->request->cookies->get('hash'));
        if (!$id or !$hash) {
            return null;
        }
        $user = $this->mapper->findById($id);
        if ($user->hash != $hash) {
            return null;
        }
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

    public function authorizeUser(User $user, $remember = true, $time = 604800)
    {
        $expires = $remember ? time() + $time : 0;
        $this->response->setCookie(
            'id',
            ['value' => $user->id, 'path' => '/', 'expires' => $expires]
        );
        $this->response->setCookie(
            'hash',
            ['value' => $user->hash, 'path' => '/', 'expires' => $expires]
        );
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

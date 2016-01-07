<?php
namespace FileSharing\Form;

use Slim\Http\Request;
use FileSharing\Helper\HashGenerator;
use FileSharing\Model\User;

class RegisterForm extends AbstractForm
{
    const NOT_UNIQUE = 'User with such email is already exists';

    public $login;
    public $email;
    public $password;

    private $user;

    public function __construct(Request $request)
    {
        $registerData = $request->post('register');
        $this->login = isset($registerData['login'])
                       ? $registerData['login'] : null;
        $this->email = isset($registerData['email'])
                       ? $registerData['email'] : null;
        $this->password = isset($registerData['password'])
                          ? $registerData['password'] : null;
        $this->user = new User;
        $this->user->login = $this->login;
        $this->user->email = $this->email;
        $this->user->salt = HashGenerator::generateSalt();
        $this->user->hash = HashGenerator::generateHash(
            $this->user->salt,
            $this->password
        );
    }

    public function validateUniqueEmail($user = null)
    {
        if (!$user) {
            return true;
        }
        $this->errorMessage = self::NOT_UNIQUE;
        return false;
    }

    public function rules()
    {
        return [
            'login' =>
                ['notEmpty' => true, 'maxLength' => 20, 'minLength' => 4],
            'email' =>
                ['notEmpty' => true, 'isEmail' => true, 'maxLength' => 50],
            'password' =>
                ['notEmpty' => true, 'minLength' => 5, 'maxLength' => 50],
        ];
    }

    public function getUser()
    {
        return $this->user;
    }
}

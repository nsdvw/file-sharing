<?php
namespace FileSharing\Form;

use Slim\Http\Request;
use FileSharing\Helper\HashGenerator;

class LoginForm extends AbstractForm
{
    const USER_NOT_FOUND = 'User not found';
    const WRONG_PASSWORD = 'Wrong password';

    private $user;

    public $email;
    public $password;

    public function __construct(Request $request)
    {
        $loginData = $request->post('login');
        $this->email = isset($loginData['email']) ? $loginData['email'] : null;
        $this->password =
            isset($loginData['password']) ? $loginData['password'] : null;
    }

    public function validatePassword($user = null)
    {
        if (!$this->user = $user) {
            $this->errorMessage = self::USER_NOT_FOUND;
            return false;
        } elseif (
            $user->hash !==
            HashGenerator::generateHash($user->salt, $this->password)
        ) {
            $this->errorMessage = self::WRONG_PASSWORD;
            return false;
        }
        return true;
    }

    protected function rules()
    {
        return [
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

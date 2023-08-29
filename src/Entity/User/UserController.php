<?php namespace Holamanola45\Www\Entity\User;

use Exception;
use Holamanola45\Www\Lib\Auth\PasswordCrypt;
use Holamanola45\Www\Lib\Auth\SessionManager;
use Holamanola45\Www\Lib\Error\BadRequestException;
use Holamanola45\Www\Lib\Error\UnauthorizedException;
use Holamanola45\Www\Lib\Http\Request;
use Holamanola45\Www\Lib\Http\Response;
use Holamanola45\Www\Lib\Utils\Timezone;

class UserController {

    private UserService $userService;

    function __construct() {
        $this->userService = new UserService();
    }

    public function getAllUsers(Request $req, Response $res) {
        $allUsers = $this->userService->findAll(array(
            'attributes' => array('id', 'username', 'created_at'),
            'limit' => 10,
            'offset' => 0
        ));

        return array(
            'users' => $allUsers
        );
    }

    public function getUser(Request $req, Response $res) {
        $user = $this->userService->findByUsername($req->params[0], ['id', 'username', 'created_at']);

        return array(
            'user' => $user
        );
    }

    public function getById(Request $req, Response $res) {
        $user = $this->userService->findById($req->params[0], array(
            'attributes' => array('id', 'username', 'created_at')
        ));

        return array(
            'user' => $user
        );
    }

    public function login(Request $req, Response $res) {
        $body = $req->getXML();

        $username = $body->username;
        $password = $body->password;

        if (!count($username) || !count($password)) {
            throw new BadRequestException('Username and password are required.');
        }

        try {
            $this->userService->beginTransaction();

            $user = $this->userService->findByUsername($username, ['id', 'username', 'password']);

            if (!isset($user->id)) {
                throw new BadRequestException("The user doesn't exist!");
            }

            $passwordValid = PasswordCrypt::verify($password, $user->password);

            if (!$passwordValid) {
                throw new UnauthorizedException('The username or password provided are incorrect.');
            }

            SessionManager::setUser($user->id, $user->username);

            $this->userService->update(array(
                'set' => array(
                    'last_login_ip' => $req->getClientIp()
                ),
                'where' => array(
                    'id' => $user->id
                )
            ));

            $this->userService->commit();

            return array(
                'sessionId' => SessionManager::getSessionId()
            );
        } catch (Exception $e) {
            $this->userService->rollback();
            throw $e;
        }
    }

    public function logout(Request $req, Response $res) {
        SessionManager::destroy();

        return;
    }

    public function whoami(Request $req, Response $res) {
        $data = SessionManager::getSessionData();

        return array(
            'userId' => $data['userId'],
            'username' => $data['username']
        );
    }

    public function createUser(Request $req, Response $res) {
        $body = $req->getXML();

        $username = $body->username;
        $password = $body->password;

        if (!count($username) || !count($password)) {
            throw new BadRequestException('Username and password are required.');
        }

        if (ctype_alnum($username)) {
            throw new BadRequestException('The username must consist of alphanumeric characters only!');
        }

        if (strlen($username) > 32) {
            throw new BadRequestException("The username can't exceed 32 characters in length!");
        }

        $ip = $req->getClientIp();

        try {
            $this->userService->beginTransaction();

            $user = $this->userService->findByUsername($username, ['id', 'username']);

            if (isset($user->id)) {
                throw new BadRequestException('The user already exists!');
            }

            $this->userService->createUser(array(
                'username' => $username,
                'password' => PasswordCrypt::encrypt($password),
                'created_at' => Timezone::getCurrentDateString(),
                'created_by_ip' => $ip
            ));

            $this->userService->commit();

            return;
        } catch (Exception $e) {
            $this->userService->rollback();
            throw $e;
        }
    }
}
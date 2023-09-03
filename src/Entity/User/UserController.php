<?php namespace Holamanola45\Www\Entity\User;

use Exception;
use Holamanola45\Www\Lib\Auth\PasswordCrypt;
use Holamanola45\Www\Lib\Auth\SessionManager;
use Holamanola45\Www\Lib\Error\BadRequestException;
use Holamanola45\Www\Lib\Error\ForbiddenException;
use Holamanola45\Www\Lib\Error\UnauthorizedException;
use Holamanola45\Www\Lib\Http\Request;
use Holamanola45\Www\Lib\Http\Response;
use Holamanola45\Www\Lib\Utils\Mailer;
use Holamanola45\Www\Lib\Utils\Timezone;

class UserController {

    private UserService $userService;

    function __construct() {
        $this->userService = new UserService();
    }

    public function getAllUsers(Request $req, Response $res) {
        $limit = $req->query_params['count'] ? $req->query_params['count'] : $_ENV['DEFAULT_ITEMS_PER_PAGE'];
        $page = $req->query_params['page'] ? $req->query_params['page'] : 1;

        if ($limit < 1 || $page < 1) {
            throw new BadRequestException('Invalid arguments.');
        }

        $count = $this->userService->getTotalRows();

        $query_options = array(
            'attributes' => array('id', 'username', 'created_at'),
            'limit' => $limit,
            'offset' => $limit * ($page - 1)
        );

        if (isset($req->query_params['qs']) && count($req->query_params['qs']) > 0) {
            $query_options['where'] = $req->query_params['qs'];
        }

        if (isset($req->query_params['order']) && count($req->query_params['order']) > 0) {
            $query_options['order'] = $req->query_params['order'];
        }

        $allUsers = $this->userService->findAll($query_options);

        return array(
            'count' => $count,
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

            $user = $this->userService->findByUsername($username, ['id', 'username', 'password', 'activate_at']);

            if (!isset($user->id)) {
                throw new BadRequestException("The user doesn't exist!");
            }

            $passwordValid = PasswordCrypt::verify($password, $user->password);

            if (!$passwordValid) {
                throw new UnauthorizedException('The username or password provided are incorrect.');
            }

            if(!isset($user->activate_at)) {
                throw new ForbiddenException('The user has not been activated yet! Please check your email.');
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
        $email = $body->email;

        if (!count($username) || !count($password) || !count($email)) {
            throw new BadRequestException('Username, password, and email are required.');
        }

        if (ctype_alnum($username)) {
            throw new BadRequestException('The username must consist of alphanumeric characters only!');
        }

        if (strlen($username) > 32) {
            throw new BadRequestException("The username can't exceed 32 characters in length!");
        }

        $clean = filter_var($email, FILTER_SANITIZE_EMAIL);

        if ($clean != $email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new BadRequestException('The email format is incorrect!');
        }

        $ip = $req->getClientIp();

        try {
            $this->userService->beginTransaction();

            $user = $this->userService->findByUsernameOrEmail($username, $email, array(
                'attributes' => ['id', 'username', 'email']
            ));

            if (isset($user->id)) {
                throw new BadRequestException('The user already exists!');
            }

            $token = md5(uniqid(time()));
            $current_date = Timezone::getCurrentDateString();

            $this->userService->createUser(array(
                'username' => $username,
                'password' => PasswordCrypt::encrypt($password),
                'created_at' => $current_date,
                'created_by_ip' => $ip,
                'email' => $clean,
                'token' => $token,
                'mail_sent_at' => $current_date
            ));

            $this->userService->commit();

            $res->toXML(array(
                'message' => 'Your account has been created. Please check your email for an activation link.'
            ));

            $mailer = new Mailer();
            $mailer->sendActivationEmail($clean, 'https://api.holamanola45.com.ar/api/user/activate/' . $token);

            return;
        } catch (Exception $e) {
            $this->userService->rollback();
            throw $e;
        }
    }

    public function activateUser(Request $req, Response $res) {
        try {
            $this->userService->beginTransaction();

            $user = $this->userService->findOne(array(
                'attributes' => ['id', 'username', 'token', 'activate_at'],
                'where' => array(
                    'token' => $req->params[0]
                )
            ));

            if (!isset($user->token)) {
                throw new BadRequestException('Invalid token.');
            }

            if (isset($user->activate_at)) {
                throw new BadRequestException('User already activated!');
            }

            $this->userService->update(array(
                'set' => array(
                    'activate_at' => Timezone::getCurrentDateString()
                ),
                'where' => array(
                    'id' => $user->id
                )
            ));

            $this->userService->commit();
        } catch (Exception $e) {
            $this->userService->rollback();
            throw $e;
        }
    }

    public function retrySendActivateEmail(Request $req, Response $res) {
        try {
            $this->userService->beginTransaction();

            $user = $this->userService->findOne(array(
                'attributes' => ['id', 'email', 'token', 'activate_at', 'mail_sent_at'],
                'where' => array(
                    'id' => $req->params[0]
                )
            ));

            if (!isset($user->id)) {
                throw new BadRequestException('User does not exist.');
            }

            if (isset($user->activate_at)) {
                throw new BadRequestException('User already active.');
            }

            $date = strtotime('now');
            $og_sent_date = strtotime($user->mail_sent_at);

            $interval = $date - $og_sent_date;

            if ($interval < 3600) {
                throw new BadRequestException('Wait at least ' . round((3600 - $interval) / 60) . ' minutes before retrying.');
            }

            $token = md5(uniqid(time()));

            $this->userService->update(array(
                'set' => array(
                    'mail_sent_at' => Timezone::getCurrentDateString(),
                    'token' => $token
                ),
                'where' => array(
                    'id' => $user->id
                )
            ));

            $this->userService->commit();

            $res->toXML(array(
                'message' => 'Mail is being resent. Please wait a few minutes.'
            ));

            $mailer = new Mailer();
            $mailer->sendActivationEmail($user->email, 'https://api.holamanola45.com.ar/api/user/activate/' . $token);

            return;

        } catch (Exception $e) {
            $this->userService->rollback();
            throw $e;
        }
    }

    public function patchUser(Request $req, Response $res) {
        try {
            $this->userService->beginTransaction();

            $user_id = $req->params[0];

            $body = $req->getXML();

            if (
                isset($body->password) ||
                isset($body->id) ||
                isset($body->created_at) ||
                isset($body->created_by_ip) ||
                isset($body->last_login_ip) ||
                isset($body->token) ||
                isset($body->activate_at)
            ) {
                throw new ForbiddenException("Fields provided are not allowed to be edited.");
            }

            $user = $this->userService->findById($user_id, array(
                'attributes' => ['id', 'username']
            ));

            if (!isset($user->id)) {
                throw new BadRequestException('User does not exist!');
            }

            if (isset($body->username)) {
                $user_by_username = $this->userService->findByUsernameOrEmail($body->username, $body->email, ['id']);

                if (isset($user_by_username->id)) {
                    throw new BadRequestException('The username or email provided is already in use.');
                }
            }

            $parsed_body = json_decode(json_encode($body), true);

            $this->userService->update(array(
                'set' => $parsed_body,
                'where' => array(
                    'id' => $user->id
                )
            ));

            $this->userService->commit();
        } catch (Exception $e) {
            $this->userService->rollback();
            throw $e;
        }
    }
}
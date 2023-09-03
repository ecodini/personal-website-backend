<?php namespace Holamanola45\Www\Entity\User;

use Holamanola45\Www\Lib\Http\Router;

class UserRoutes {
    public static $prefix = '/api/user';

    public static function addRoutes(Router $router) {
        // auth endpoints
        $router->add(self::$prefix. '/login', 'post', UserController::class, 'login');
        $router->add(self::$prefix. '/logout', 'post', UserController::class, 'logout', true);
        $router->add(self::$prefix. '/whoami', 'get', UserController::class, 'whoami', true);

        $router->add(self::$prefix. '/activate/([A-Za-z0-9_]+$)', 'post', UserController::class, 'activateUser');
        $router->add(self::$prefix. '/create', 'post', UserController::class, 'createUser');
        $router->add(self::$prefix. '/([0-9]*)/resend', 'post', UserController::class, 'retrySendActivateEmail');

        // get users
        $router->add(self::$prefix. '/name/([^/]*$)', 'get', UserController::class, 'getUser', true);
        $router->add(self::$prefix. '/([0-9]*)', 'get', UserController::class, 'getById', true);
        $router->add(self::$prefix. '/([0-9]*)', 'patch', UserController::class, 'patchUser', true);
        $router->add(self::$prefix, 'get', UserController::class, 'getAllUsers', true);
    }
}
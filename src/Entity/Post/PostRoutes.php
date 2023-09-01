<?php namespace Holamanola45\Www\Entity\Post;

use Holamanola45\Www\Lib\Http\Router;

class PostRoutes {
    public static $prefix = '/api/post';

    public static function addRoutes(Router $router) {
        $router->add(self::$prefix, 'get', PostController::class, 'getAllPosts', true);
    }
}
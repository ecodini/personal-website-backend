<?php namespace Holamanola45\Www\Entity\Post;

use Holamanola45\Www\Lib\Http\Router;

class PostRoutes {
    public static $prefix = '/api/post';

    public static function addRoutes(Router $router) {
        $router->add(self::$prefix, 'get', PostController::class, 'getAllPosts', true);
        $router->add(self::$prefix . '/([0-9]*)', 'get', PostController::class, 'getPostById', true);

        $router->add(self::$prefix, 'post', PostController::class, 'createPost', true);
    }
}
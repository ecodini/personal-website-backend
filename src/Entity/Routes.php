<?php namespace Holamanola45\Www\Entity;

use Holamanola45\Www\Entity\Post\PostRoutes;
use Holamanola45\Www\Entity\User\UserRoutes;
use Holamanola45\Www\Lib\Http\Router;

class Routes {
    public static function addRoutes(Router $router) {
        UserRoutes::addRoutes($router);
        PostRoutes::addRoutes($router);
    }
}
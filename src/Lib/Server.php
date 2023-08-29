<?php namespace Holamanola45\Www\Lib;

use Holamanola45\Www\Lib\Auth\SessionManager;
use Holamanola45\Www\Lib\Http\Router;
use Holamanola45\Www\Lib\Utils\Logger;

class Server {
    function __construct(Router $router) {
        $this->router = $router;
    }

    private Router $router;

    public function run()
    {
        SessionManager::load();
        $this->router->route();
        Logger::startLogger();
    }
}
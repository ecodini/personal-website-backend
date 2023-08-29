<?php
require __DIR__ . '/vendor/autoload.php';

use Dotenv\Dotenv;
use Holamanola45\Www\Lib\Server;
use Holamanola45\Www\Lib\Http\Router;
use Holamanola45\Www\Entity\Routes;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$router = new Router();
$server = new Server($router);

Routes::addRoutes($router);

$server->run();
<?php namespace Holamanola45\Www\Lib\Http;

use Holamanola45\Www\Lib\Auth\SessionManager;
use Holamanola45\Www\Lib\Error\GenericHttpException;
use Holamanola45\Www\Lib\Error\InternalServerErrorException;
use Holamanola45\Www\Lib\Error\NotFoundException;
use Throwable;

class Router
{
    private $routes = array();

    public static function get($route, $callback)
    {
        if (strcasecmp($_SERVER['REQUEST_METHOD'], 'GET') !== 0) {
            return;
        }

        return self::on($route, $callback);
    }

    public static function post($route, $callback)
    {
        if (strcasecmp($_SERVER['REQUEST_METHOD'], 'POST') !== 0) {
            return;
        }

        return self::on($route, $callback);
    }

    public static function on($regex, $cb_array)
    {
        $params = $_SERVER['REQUEST_URI'];

        $question_pos = strpos($params, '?');

        if ($question_pos) {
            $params = substr($params, 0, $question_pos);
        }

        if (substr($params, -1) == '/') {
            $params = rtrim($params, '/');
        }

        $params = (stripos($params, "/") !== 0) ? "/" . $params : $params;
        $regex = str_replace('/', '\/', $regex);
        $is_match = preg_match('/^' . ($regex) . '$/', $params, $matches, PREG_OFFSET_CAPTURE);

        if ($is_match) {
            if ($cb_array['auth']) {
                SessionManager::verifyLoggedIn();
            }

            $explode_params = explode('&', $_SERVER['QUERY_STRING']);

            $query_params = array();

            foreach ($explode_params as $val) {
                $value = explode('=', $val);

                $query_params[$value[0]] = $value[1];
            }
            
            array_shift($matches);

            $params = array_map(function ($param) {
                return $param[0];
            }, $matches);

            $cb = array(new $cb_array['class'](), $cb_array['func']);

            $res = new Response();

            $response = $cb(new Request($params, $query_params), $res);

            $res->toXML($response);

            return true;
        }

        return false;
    }

    public function add($path, $method, $controller, $func, bool $auth = false) {
        $this->routes[] = array(
            "method" => $method,
            "path" => $path,
            "controller" => $controller,
            "func" => $func,
            "auth" => $auth
        );
    }

    public function route() {
        try {
            foreach ($this->routes as &$value) {             
                $matched = call_user_func(
                    'Holamanola45\Www\Lib\Http\Router::'.$value["method"], 
                    $value['path'], 
                    array(
                        "class" => $value['controller'], 
                        "func" => $value['func'],
                        "auth" => $value['auth']
                    )
                );

                if ($matched) {
                    return;
                }
            }

            throw new NotFoundException('The URL requested was not found on this server.');
        } catch (GenericHttpException $e) {
            $res = new Response();
            $e->toXML($res);
        } catch (Throwable $e) {
            $e = new InternalServerErrorException($e->getMessage());
            $res = new Response();
            $e->toXML($res);
        }
    }
}
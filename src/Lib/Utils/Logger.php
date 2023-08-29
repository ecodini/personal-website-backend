<?php namespace Holamanola45\Www\Lib\Utils;

use Holamanola45\Www\Lib\Config;
use Monolog\ErrorHandler;
use Monolog\Handler\DeduplicationHandler;
use Monolog\Handler\StreamHandler;

class Logger extends \Monolog\Logger
{
    private static $loggers = [];

    public function __construct($key = "app", $config = null)
    {
        parent::__construct($key);

        if (empty($config)) {
            $LOG_PATH = Config::get('LOG_PATH', __DIR__ . '/../../logs');
            $config = [
                'logFile' => "{$LOG_PATH}/{$key}.log",
                'logLevel' => \Monolog\Logger::DEBUG
            ];
        }

        $streamHandler = new StreamHandler($config['logFile']);
        $this->pushHandler(new DeduplicationHandler($streamHandler));
    }

    public static function getInstance($key = "app", $config = null)
    {
        if (empty(self::$loggers[$key])) {
            self::$loggers[$key] = new Logger($key, $config);
        }

        return self::$loggers[$key];
    }

    public static function startLogger()
    {

        $LOG_PATH = Config::get('LOG_PATH', __DIR__ . '/../../logs');

        self::$loggers['error'] = new Logger('errors');
        ErrorHandler::register(self::$loggers['error']);

        self::$loggers['request'] = new Logger('request');
        self::$loggers['request']->info("REQUEST - " . $_SERVER['REQUEST_METHOD'] . ' ' . $_SERVER['REQUEST_URI'] . ' - ' . $_SERVER['HTTP_X_FORWARDED_FOR'] . ' - STATUS: ' . http_response_code());
    }
}
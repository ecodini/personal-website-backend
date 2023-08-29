<?php namespace Holamanola45\Www\Lib\Error;

use Throwable;

class UnauthorizedException extends GenericHttpException {
    public function __construct($message = null, $code = 0, Throwable $previous = null) {
        if (!isset($message)) {
            $message = 'Unauthorized Exception';
        }

        parent::__construct($message, $code, $previous, 401, false);
    }
}
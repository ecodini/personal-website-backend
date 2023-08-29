<?php namespace Holamanola45\Www\Lib\Error;

use Throwable;

class InternalServerErrorException extends GenericHttpException {
    public function __construct($message = null, $code = 0, Throwable $previous = null) {
        if (!isset($message)) {
            $message = 'Internal Server Error Exception';
        }

        parent::__construct($message, $code, $previous, 500, true);
    }
}
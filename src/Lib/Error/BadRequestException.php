<?php namespace Holamanola45\Www\Lib\Error;

use Throwable;

class BadRequestException extends GenericHttpException {
    public function __construct($message = null, $code = 0, Throwable $previous = null) {
        if (!isset($message)) {
            $message = 'Bad Request Exception';
        }

        parent::__construct($message, $code, $previous, 400, false);
    }
}
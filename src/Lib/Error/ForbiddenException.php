<?php namespace Holamanola45\Www\Lib\Error;

use Throwable;

class ForbiddenException extends GenericHttpException {
    public function __construct($message = null, $code = 0, Throwable $previous = null) {
        if (!isset($message)) {
            $message = 'Forbidden Exception';
        }

        parent::__construct($message, $code, $previous, 403, false);
    }
}
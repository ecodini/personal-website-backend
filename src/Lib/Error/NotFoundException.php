<?php namespace Holamanola45\Www\Lib\Error;

use Throwable;

class NotFoundException extends GenericHttpException {
    public function __construct($message = null, $code = 0, Throwable $previous = null) {
        if (!isset($message)) {
            $message = 'Not Found Exception';
        }

        parent::__construct($message, $code, $previous, 404, false);
    }
}
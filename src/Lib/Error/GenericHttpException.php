<?php namespace Holamanola45\Www\Lib\Error;
      
use Exception;
use Holamanola45\Www\Lib\Http\Response;
use Throwable;

class GenericHttpException extends Exception {
    public function __construct($message = null, $code = 0, Throwable $previous = null, $httpCode, bool $stackTrace) {
        if (!isset($message)) {
            $message = 'Bad Request Exception';
        }

        $this->statusCode = $httpCode;
        $this->stackTrace = $stackTrace;

        parent::__construct($message, $code, $previous);
    }
    
    public int $statusCode;

    public string $defaultMessage;

    public bool $stackTrace;

    public function toXML(Response $res) {
        $res->status($this->statusCode);

        $data = array(
            'status' => $this->statusCode,
            'message' => isset($this->message) ? $this->message : $this->defaultMessage
        );

        if ($this->stackTrace) {
            $data['stack'] = $this->getTraceAsString();
        }

        $res->toXML($data);

        $res->closeConnection();
    }
}
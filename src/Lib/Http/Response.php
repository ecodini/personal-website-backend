<?php namespace Holamanola45\Www\Lib\Http;

use Holamanola45\Www\Lib\Utils\XMLHelper;

class Response
{
    private $status = 200;

    public function status(int $code)
    {
        $this->status = $code;
        return $this;
    }
    
    public function toJSON($data = [])
    {
        http_response_code($this->status);
        header('Content-Type: application/json');
        echo json_encode($data);
    }

    public function toXML($data = []) {
        http_response_code($this->status);
        header('Content-Type: application/xml');

        $data['status'] = $this->status;

        if (!isset($data['message'])) {
            if ($this->status < 400) {
                $data['message'] = 'OK';
            } else {
                $data['message'] = 'An error has ocurred while processing your request.';
            }
        }

        $xml = XMLHelper::arrayToXML($data);
        echo $xml->asXML();
    }

    public function closeConnection() {
        session_write_close();
        ignore_user_abort();
        fastcgi_finish_request();
    }
}
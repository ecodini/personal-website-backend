<?php namespace Holamanola45\Www\Lib\Http;

use SimpleXMLElement;

class Request
{
    public $params;

    public $query_params;
    public $reqMethod;
    public $contentType;

    public function __construct($params = [], $query_params = [])
    {
        $this->params = $params;
        $this->reqMethod = trim($_SERVER['REQUEST_METHOD']);
        $this->contentType = !empty($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';
        $this->query_params = $query_params;
    }

    public function getBody()
    {
        if ($this->reqMethod !== 'POST') {
            return '';
        }

        $body = [];
        foreach ($_POST as $key => $value) {
            $body[$key] = filter_input(INPUT_POST, $key, FILTER_SANITIZE_SPECIAL_CHARS);
        }

        return $body;
    }

    public function getJSON()
    {
        if ($this->reqMethod !== 'POST') {
            return [];
        }

        if (strcasecmp($this->contentType, 'application/json') !== 0) {
            return [];
        }

        // Receive the RAW post data.
        $content = trim(file_get_contents("php://input"));
        $decoded = json_decode($content);

        return $decoded;
    }

    public function getXML() {
        if ($this->reqMethod !== 'POST') {
            return [];
        }

        if (strcasecmp($this->contentType, 'application/xml') !== 0) {
            return [];
        }

        $content = trim(file_get_contents("php://input"));

        return new SimpleXMLElement(trim(file_get_contents("php://input")));
    }

    public function getClientIp() {
        return $_SERVER["HTTP_CF_CONNECTING_IP"];
    }
}
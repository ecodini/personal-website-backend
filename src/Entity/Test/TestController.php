<?php namespace Holamanola45\Www\Entity\Test;

use Exception;
use Holamanola45\Www\Lib\Error\BadRequestException;
use Holamanola45\Www\Lib\Error\GenericHttpException;
use Holamanola45\Www\Lib\Error\InternalServerErrorException;
use Holamanola45\Www\Lib\Http\Request;
use Holamanola45\Www\Lib\Http\Response;
use Throwable;

class TestController
{
    public function helloWorld(Request $req, Response $res) {
        return array(
            'message' => 'Hello world!'
        );
    }

    public function postTest(Request $req, Response $res)
    { 
        $xml = $req->getXML();

        $response = [];

        if ($xml->test == 'HOLA') {
            $response = array(
                'message' => 'ke onda'
            );
        }

        $res->status(201);
        return $response;
    }
}
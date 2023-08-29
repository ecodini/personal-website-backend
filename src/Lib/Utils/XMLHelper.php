<?php namespace Holamanola45\Www\Lib\Utils;
      use SimpleXMLElement;

class XMLHelper {
    public static function arrayToXml(array $array, string $rootElement = null, SimpleXMLElement $xml = null): SimpleXMLElement {
        $_xml = $xml;
         
        if ($_xml === null) {
            $_xml = new SimpleXMLElement($rootElement !== null ? $rootElement : '<root/>');
        }
         
        foreach ($array as $k => $v) {
             
            if (is_array($v)) {
                $id = NULL;

                if (is_int($k)) {
                    $id = $k;
                    $k = 'row';
                }

                $subnode = $_xml->addChild($k);

                if (isset($id)) {
                    $subnode->addAttribute('num', $id);
                }

                self::arrayToXml($v, $k, $subnode);
            } else {
                $_xml->addChild($k, $v);
            }
        }

        return $_xml;
    }
}
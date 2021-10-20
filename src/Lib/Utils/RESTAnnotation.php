<?php

namespace Revosystems\RedsysGateway\Lib\Utils;

class RESTAnnotation
{
    private static function getAnnotation($object, $name)
    {
        $doc = $object->getDocComment();
        preg_match('#@' . $name . '=(.+)(\s)*(\r)*\n#s', $doc, $annotations);
        if (! is_array($annotations) || ! sizeof($annotations) >= 2) {
            return null;
        }
        return trim(explode(" ", $annotations[1])[0]);
    }

    public static function getXmlElem($object)
    {
        return RESTAnnotation::getAnnotation($object, "XML_ELEM");
    }

    public static function getXmlClass($object)
    {
        return RESTAnnotation::getAnnotation($object, "XML_CLASS");
    }
}

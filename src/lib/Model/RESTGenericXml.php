<?php

namespace App\Services\Redsys\Lib\Model;

use App\Services\Redsys\Lib\Utils\RESTAnnotation;
use Exception;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;

abstract class RESTGenericXml
{
    public function toJson($arr = [], $encoded = true)
    {
        $thisClass = new ReflectionClass(get_class($this));
        if (! RESTAnnotation::getXmlElem($thisClass)) {
            return $encoded ? json_encode([]) : [];
        }
        foreach ($thisClass->getProperties() as $prop) {
            $xmlClass = RESTAnnotation::getXmlClass($prop);
            if ($xmlClass !== null) {
                $xmlElem = RESTAnnotation::getXmlElem($prop);
                $obj     = $this->getPropertyValue($prop);
                if ($obj !== null && $xmlElem !== null) {
                    $propClass     = new ReflectionClass($xmlClass);
                    $val           = $propClass->getMethod("toJson")->invoke($obj, [], false);
                    $arr[$xmlElem] = $val;
                }
            } else {
                $xmlElem = RESTAnnotation::getXmlElem($prop);
                if ($xmlElem !== null) {
                    $obj = $this->getPropertyValue($prop);
                    if ($obj !== null) {
                        $arr[$xmlElem] = $obj;
                    }
                }
            }
        }

        try {
            if (! $params = $thisClass->getProperty("parameters")) {
                return $encoded ? json_encode($arr) : $arr;
            }
            if (! $values = $this->getPropertyValue($params)) {
                return $encoded ? json_encode($arr) : $arr;
            }
            foreach ($values as $key => $value) {
                $arr[$key] = $value;
            }
        } catch (Exception $e) {
        } finally {
            return $encoded ? json_encode($arr) : $arr;
        }
    }

    public function parseJson($json)
    {
        logger("[REDSYS] Response JSON: {$json}");
        $arr = json_decode($json, true);
        foreach ((new ReflectionClass(get_class($this)))->getProperties() as $prop) {
            $xmlClass = RESTAnnotation::getXmlClass($prop);
            if ($xmlClass !== null) {
                $propClass = new ReflectionClass($xmlClass);
                $xmlElem   = RESTAnnotation::getXmlElem($prop);

                if ($xmlElem !== null && isset($arr[$xmlElem])) {
                    $obj = $propClass->newInstance();

                    $propClass->getMethod("parseJson")->invoke($obj, $arr[$xmlElem]);

                    $this->setPropertyValue($prop, $obj);
                    unset($arr[$xmlElem]);
                }
            } else {
                $xmlElem = RESTAnnotation::getXmlElem($prop);
                if ($xmlElem !== null && isset($arr[$xmlElem])) {
                    $tagContent = $arr[$xmlElem];
                    if ($tagContent !== null) {
                        $this->setPropertyValue($prop, $tagContent);
                        unset($arr[$xmlElem]);
                    }
                }
            }
        }
    }

    private function setPropertyValue(ReflectionProperty $prop, $value)
    {
        if (! $setter = new ReflectionMethod(get_class($this), "set{$this->getPropertyName($prop)}")) {
            return null;
        }
        $setter->invoke($this, $value);
    }

    private function getPropertyValue(ReflectionProperty $prop)
    {
        if (! $getter = new ReflectionMethod(get_class($this), "get{$this->getPropertyName($prop)}")) {
            return null;
        }
        return $getter->invoke($this);
    }

    private function getPropertyName(ReflectionProperty $prop): string
    {
        return strtoupper(substr($prop->getName(), 0, 1)) . substr($prop->getName(), 1, strlen($prop->getName()) - 1);
    }
}

<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/18
 * Time: 20:31
 */

namespace lanlj\fw\util;

use DOMDocument;
use DOMElement;
use Exception;
use lanlj\fw\core\Arrays;
use ReflectionObject;

final class ArrayUtil
{
    const XML_ROOT = "root";
    const XML_CDATA = "cdata";
    const XML_FORMAT = "format";

    /**
     * @param array $arr
     * @param DOMDocument $dom
     * @param DOMElement $node
     * @param array $config
     * @return string
     */
    public static function toXML(array $arr, $dom = null, $node = null, array $config = null)
    {
        $config = new Arrays($config);
        if (empty($dom)) $dom = new DOMDocument("1.0", "UTF-8");
        $dom->formatOutput = $config->get(self::XML_FORMAT, true);
        if (empty($node)) {
            $node = $dom->createElement($config->get(self::XML_ROOT, "xml"));
            $dom->appendChild($node);
        }
        foreach ($arr as $key => $val) {
            $childNode = $dom->createElement(is_string($key) ? $key : "node");
            $node->appendChild($childNode);
            if (is_array($val) || is_object($val))
                self::toXML(ArrayUtil::toArray($val, false, true), $dom, $childNode, $config->getArray());
            else {
                if (is_string($val) && $config->get(self::XML_CDATA, false))
                    $text = $dom->createCDATASection($val);
                else $text = $dom->createTextNode($val);
                $childNode->appendChild($text);
            }
        }
        return $dom->saveXML();
    }

    /**
     * @param mixed $var
     * @param bool $onlyPublic
     * @param bool $all
     * @return array
     */
    public static function toArray($var, $onlyPublic = true, $all = false)
    {
        $arr = array();
        if ($all && is_array($var)) {
            foreach ($var as $k => $v) {
                $arr[$k] = is_array($v) || is_object($v) ? self::toArray($v, $onlyPublic, $all) : $v;
            }
            return $arr;
        }
        if (is_array($var)) return $var;
        if (!is_object($var)) {
            return [$var];
        }
        $ref = new ReflectionObject($var);
        foreach ($ref->getProperties() as $property) {
            $name = $property->getName();
            $value = null;
            $collect = true;
            if ($onlyPublic && !$property->isPublic()) $collect = false;
            if ($collect) {
                $mn = ucwords(str_replace('_', '', $name));
                $other_ways = true;
                if (method_exists($var, $method_name = "get$mn") || method_exists($var, $method_name = "is$mn")) {
                    try {
                        $method = $ref->getMethod($method_name);
                        $method->setAccessible(true);
                        $value = $method->invoke($var);
                        $other_ways = false;
                    } catch (Exception $e) {
                    }
                }
                if ($other_ways) {
                    if (method_exists($var, '__get'))
                        $value = $var->$name;
                    else {
                        $property->setAccessible(true);
                        $value = $property->getValue($var);
                    }
                }
            }
            if ($all && (is_array($value) || is_object($value))) $value = self::toArray($value, $onlyPublic, $all);
            if (!is_null($value)) $arr[$name] = $value;
        }
        return $arr;
    }
}
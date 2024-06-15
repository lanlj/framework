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

class ArrayUtil
{
    const XML_ROOT = "root";
    const XML_CDATA = "cdata";
    const XML_FORMAT = "format";

    /**
     * The default value is true,
     * When calling the "toArray" method,
     * The property with a "NULL" value will be ignored. Otherwise,
     * @var bool
     */
    static bool $objectIgnoreNULL = true;

    /**
     * @param array $arr
     * @param DOMDocument|null $dom
     * @param DOMElement|null $node
     * @param array|null $config
     * @return string
     */
    public static function toXML(array $arr, DOMDocument $dom = null, DOMElement $node = null, array $config = null): string
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
     * @param bool $db
     * @return array
     */
    public static function toArray($var, bool $onlyPublic = true, bool $all = false, bool $db = false): array
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
            if (!$onlyPublic || $property->isPublic()) {
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
            if (!self::$objectIgnoreNULL || !is_null($value)) $arr[!$db ? $name : BeanUtil::getColumnName($property, $db)] = $value;
        }
        return $arr;
    }

    /**
     * 获取嵌套数组值
     * @param array $array
     * @param array $keys
     * @return mixed|null
     */
    public static function getNestedValue(array $array, array $keys)
    {
        if (count($keys) == 0) return null;
        $key = array_shift($keys);
        if (!array_key_exists($key, $array)) return null;
        if (count($keys) == 0) return $array[$key];
        if (is_array($array[$key])) return self::getNestedValue($array[$key], $keys);
        return null;
    }
}
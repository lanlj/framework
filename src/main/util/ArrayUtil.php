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
use Error;
use Exception;
use lanlj\fw\base\Arrays;
use lanlj\fw\bean\BeanArray;
use ReflectionObject;
use stdClass;

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
                $arr[$k] = is_array($v) || is_object($v) ? self::toArray($v, $onlyPublic, $all, $db) : $v;
            }
            return $arr;
        }
        if (is_array($var)) return $var;
        if (!is_object($var)) return [$var];
        if ($var instanceof stdClass) return self::toArray(get_object_vars($var), $onlyPublic, $all, $db);
        if ($var instanceof BeanArray) return self::toArray(
            call_user_func(array($var, 'toArray'), $onlyPublic, $all, $db), $onlyPublic, $all, $db
        );
        $ref = new ReflectionObject($var);
        $properties = $ref->getProperties();
        $parent = $ref->getParentClass();
        while (true) {
            if ($parent === false) break;
            $properties = array_merge($parent->getProperties(), $properties);
            $parent = $parent->getParentClass();
        }
        foreach ($properties as $property) {
            $name = $property->getName();
            $value = null;
            if (!$onlyPublic || $property->isPublic()) {
                $otherWays = true;
                $_name = str_replace('_', '', $name);
                $mn1 = ucwords($_name);
                $mn2 = strtoupper($_name);
                if (
                    $ref->hasMethod($mn = "get$mn1") || $ref->hasMethod($mn = "is$mn1") ||
                    $ref->hasMethod($mn = "get$mn2") || $ref->hasMethod($mn = "is$mn2")
                ) {
                    try {
                        $method = $ref->getMethod($mn);
                        $method->setAccessible(true);
                        $value = $method->invoke($var);
                        $otherWays = false;
                    } catch (Error | Exception $e) {
                    }
                }
                if ($otherWays) {
                    if (method_exists($var, '__get'))
                        $value = $var->$name;
                    else {
                        $property->setAccessible(true);
                        if ($property->isInitialized($var))
                            $value = $property->getValue($var);
                    }
                }
            }
            if ($all && (is_array($value) || is_object($value))) $value = self::toArray($value, $onlyPublic, $all, $db);
            if (!self::$objectIgnoreNULL || !is_null($value)) $arr[!$db ? $name : DBUtil::getColumnName($property, $db)] = $value;
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
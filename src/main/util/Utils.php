<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/31
 * Time: 3:53
 */

namespace lanlj\fw\util;

class Utils
{
    const TYPE_BOOL = "boolean";
    const TYPE_INT = "integer";
    const TYPE_FLOAT = "float";
    const TYPE_STRING = "string";
    const TYPE_ARRAY = "array";
    const TYPE_OBJECT = "object";
    const TYPE_RESOURCE = "resource";
    const TYPE_NULL = "null";
    const TYPE_UNKNOWN = "unknown";

    /**
     * 生成guid
     * @return string
     */
    public static function guid(): string
    {
        mt_srand((double)microtime() * 10000); //optional for php 4.2.0 and up.
        return md5(uniqid(rand(), true));
    }

    /**
     * @param array|object $var
     * @param string|int $key
     * @param mixed $default
     * @return mixed
     */
    public static function getDefault($var, $key, $default = null)
    {
        if (is_object($var)) $var = ArrayUtil::toArray($var, false, true);
        $v = array_key_exists($key, $var) ? $var[$key] : $default;
        return Utils::getVal($v, $default);
    }

    /**
     * @param mixed $var
     * @param mixed $default
     * @return mixed
     */
    public static function getVal($var, $default = null)
    {
        if (self::isEmpty($var)) return $default;
        if (is_null($default)) return $var;
        if (self::setType($var, self::getType($default))) return $var;
        return $default;
    }

    /**
     * @param mixed $var
     * @return bool
     */
    public static function isEmpty($var): bool
    {
        if (is_null($var)) return true;
        if (is_string($var) && trim($var) === "") return true;
        return false;
    }

    /**
     * @param mixed &$var
     * @param string $type
     * @return bool
     */
    public static function setType(&$var, ?string $type): bool
    {
        switch ($type) {
            case "bool":
            case self::TYPE_BOOL:
                return is_bool($var = BooleanUtil::toBool($var));
            case "int":
            case self::TYPE_INT:
                return is_int($var = intval($var));
            case "double":
            case self::TYPE_FLOAT:
                return is_float($var = floatval($var));
            case self::TYPE_STRING:
                return is_string($var = StringUtil::toString($var));
            case self::TYPE_ARRAY:
                return is_array($var = ArrayUtil::toArray($var));
            default:
                return false;
        }
    }

    /**
     * Returns the type of the var passed.
     * @param mixed $var Variable
     * @return string Type of variable
     */
    public static function getType($var): ?string
    {
        if (is_bool($var)) return self::TYPE_BOOL;
        if (is_int($var)) return self::TYPE_INT;
        if (is_float($var)) return self::TYPE_FLOAT;
        if (is_string($var)) return self::TYPE_STRING;
        if (is_array($var)) return self::TYPE_ARRAY;
        if (is_object($var)) return self::TYPE_OBJECT;
        if (is_resource($var)) return self::TYPE_RESOURCE;
        if (is_null($var)) return self::TYPE_NULL;
        return self::TYPE_UNKNOWN;
    }
}
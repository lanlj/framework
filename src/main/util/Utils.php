<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/31
 * Time: 3:53
 */

namespace lanlj\util;

final class Utils
{
    /**
     * 生成guid
     * @return string
     */
    public static function guid()
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
        if (is_object($var))
            $var = ArrayUtil::toArray($var, false, true);
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
    public static function isEmpty($var)
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
    public static function setType(&$var, $type)
    {
        switch ($type) {
            case "bool":
            case "boolean":
                return is_bool($var = BooleanUtil::toBool($var));
                break;
            case "int":
            case "integer":
                return is_int($var = intval($var));
                break;
            case "float":
            case "double":
                return is_float($var = floatval($var));
                break;
            case "string":
                return is_string($var = StringUtil::toString($var));
                break;
            case "array":
                return is_array($var = ArrayUtil::toArray($var));
                break;
            default:
                return false;
                break;
        }
    }

    /**
     * Returns the type of the var passed.
     * @param mixed $var Variable
     * @return string Type of variable
     */
    public static function getType($var)
    {
        if (is_bool($var)) return "boolean";
        if (is_int($var)) return "integer";
        if (is_float($var)) return "float";
        if (is_string($var)) return "string";
        if (is_array($var)) return "array";
        if (is_object($var)) return "object";
        if (is_resource($var)) return "resource";
        if (is_null($var)) return "NULL";
        return "unknown type";
    }
}
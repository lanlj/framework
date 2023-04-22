<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/18
 * Time: 17:41
 */

namespace lanlj\fw\util;

final class BooleanUtil
{
    /**
     * 变量是否为真（bool类型）
     * @param mixed $var
     * @return bool
     */
    public static function isTrue($var)
    {
        return self::toBool($var) == true;
    }

    /**
     * 将变量转换为bool类型
     * @param mixed $var
     * @return bool
     */
    public static function toBool($var)
    {
        if (!is_string($var)) return (bool)$var;
        switch (strtolower($var)) {
            case '1':
            case 'true':
            case 'on':
            case 'yes':
            case 'y':
                return true;
            default:
                return false;
        }
    }

    /**
     * 变量是否为bool类型
     * @param mixed $var
     * @return bool
     */
    public static function isBool($var)
    {
        if (is_bool($var)) return true;
        if (!is_numeric($var) && !is_string($var)) return false;
        switch (strtolower($var)) {
            case '1':
            case '0':
            case 'true':
            case 'false':
            case 'on':
            case 'off':
            case 'yes':
            case 'no':
            case 'y':
            case 'n':
                return true;
            default:
                return false;
        }
    }
}
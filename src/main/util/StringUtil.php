<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/18
 * Time: 20:28
 */

namespace lanlj\fw\util;

use lanlj\fw\core\Arrays;

class StringUtil
{
    /**
     * 字符串是否base64编码
     * @param string|null $str
     * @return bool
     */
    public static function isBase64(?string $str): bool
    {
        return str_replace('=', '', $str) == str_replace('=', '', base64_encode(base64_decode($str)));
    }

    /**
     * @param mixed $var
     * @param bool $json
     * @return string
     */
    public static function toString($var, bool $json = false): string
    {
        $s = null;
        $oa = null;
        if (is_string($var)) $s = $var;
        elseif (is_object($var) && method_exists($var, "__toString")) $s = $var->__toString();
        elseif (is_array($var) || is_object($var)) $oa = new Arrays($var);
        else $s = strval($var);
        if ($json) return is_null($s) ? JsonUtil::toJsonString($oa->getArray()) : (JsonUtil::isJson($s) ? $s : JsonUtil::toJsonString($s));
        return is_null($s) ? $oa->toQueryString("&", "arg") : $s;
    }
}
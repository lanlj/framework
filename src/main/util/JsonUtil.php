<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/30
 * Time: 1:43
 */

namespace lanlj\fw\util;


class JsonUtil
{
    /**
     * json数据格式化
     * @param mixed $data 数据
     * @param string $indent 缩进字符，默认4个空格
     * @return string
     */
    public static function jsonFormat($data, string $indent = null): string
    {
        $data = self::mergeOrFormatJson($data, false, false);

        if (version_compare(PHP_VERSION, "5.4", ">="))
            return json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);

        if (!is_array($data)) return json_encode($data);

        //对数组中每个元素递归进行urlencode操作，保护中文字符
        array_walk_recursive($data, 'self::jsonFormatProtect');

        //json encode
        $data = json_encode($data);
        //将urlencode的内容进行urldecode
        $data = urldecode($data);

        //缩进处理
        $ret = '';
        $pos = 0;
        $length = strlen($data);
        $indent = isset($indent) ? $indent : '    ';
        $newline = "\n";
        $prevchar = '';
        $outofquotes = true;

        for ($i = 0; $i <= $length; $i++) {
            $char = substr($data, $i, 1);
            if ($char == '"' && $prevchar != '\\') {
                $outofquotes = !$outofquotes;
            } elseif (($char == '}' || $char == ']') && $outofquotes) {
                $ret .= $newline;
                $pos--;
                for ($j = 0; $j < $pos; $j++) {
                    $ret .= $indent;
                }
            }
            $ret .= $char;
            if (($char == ',' || $char == '{' || $char == '[') && $outofquotes) {
                $ret .= $newline;
                if ($char == '{' || $char == '[') {
                    $pos++;
                }
                for ($j = 0; $j < $pos; $j++) {
                    $ret .= $indent;
                }
            }
            $prevchar = $char;
        }
        return $ret;
    }

    /**
     * @param mixed $data
     * @param bool $mergeJsonString
     * @param bool $formatErrorJson
     * @return mixed
     */
    protected static function mergeOrFormatJson($data, bool $mergeJsonString, bool $formatErrorJson)
    {
        if (is_object($data)) $data = ArrayUtil::toArray($data, false, true);

        if ($mergeJsonString && is_array($data)) {
            foreach ($data as $key => $value) {
                if (is_string($value)) {
                    if (!self::isJson($value)) {
                        if ($formatErrorJson && self::isJson($value = self::formatErrorJson($value)))
                            $data[$key] = json_decode($value);
                    } else $data[$key] = json_decode($value);
                }
                if (is_array($value) || is_object($value))
                    $data[$key] = self::mergeOrFormatJson($value, $mergeJsonString, $formatErrorJson);
            }
        }
        if ($formatErrorJson && is_string($data)) {
            if (self::isJson($json = self::formatErrorJson($data)))
                $data = json_decode($json);
        }
        return $data;
    }

    /**
     * 是否是合格的json数据
     * @param string $string
     * @return bool
     */
    public static function isJson(?string $string): bool
    {
        json_decode($string);
        return json_last_error() == JSON_ERROR_NONE;
    }

    /**
     * 格式化错误的json数据，使其能被json_decode()解析
     * 不支持键名有中文、引号、花括号、冒号
     * 不支持键值有冒号*
     * @param string $data
     * @param bool $quotesKey
     * @return string
     */
    protected static function formatErrorJson(?string $data, bool $quotesKey = false): string
    {
        $json = str_replace('\'', '"', $data); //替换单引号为双引号
        $json = str_replace(array('\\"'), array('<|YH|>'), $json); //替换
        $json = preg_replace('/(\w+):[ {]?((?<YinHao>"?).*?\k<YinHao>[,}]?)/is', '"$1": $2', $json); //若键名没有双引号则添加
        if ($quotesKey) {
            $json = preg_replace('/("\w+"): ?([^"\s]+)([,}])[\s]?/is', '$1: "$2"$3', $json); //给键值添加双引号
        }
        $json = str_replace(array('<|YH|>'), array('\\"'), $json); //还原替换
        return $json;
    }

    /**
     * 转换为json字符串
     * @param mixed $things
     * @param bool $mergeJsonString
     * @param bool $formatErrorJson
     * @return string
     */
    public static function toJsonString($things, bool $mergeJsonString = false, bool $formatErrorJson = false): string
    {
        return json_encode(self::mergeOrFormatJson($things, $mergeJsonString, $formatErrorJson));
    }

    /**
     * 将json字符串转换
     * @param string $jsonString
     * @param bool $assoc
     * @return object|array|null
     */
    public static function toJson(?string $jsonString, bool $assoc = false)
    {
        $json = json_decode($jsonString, $assoc);
        if (json_last_error() == JSON_ERROR_NONE) return $json;
        return null;
    }

    /**
     * 将数组元素进行urlencode
     * @param string $val
     */
    protected static function jsonFormatProtect(?string &$val)
    {
        if ($val !== true && $val !== false && $val !== null) {
            $val = urlencode($val);
        }
    }
}
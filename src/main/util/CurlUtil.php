<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/22
 * Time: 14:15
 */

namespace lanlj\fw\util;

class CurlUtil
{
    /**
     * 解析头部字符串
     * @param string|null $header
     * @return array
     */
    public static function parseHeader(?string $header): array
    {
        $headArr = explode(PHP_EOL, $header);
        $headers = array();
        foreach ($headArr as $value) {
            if (Utils::isEmpty($value))
                continue;
            $item = explode(': ', $value, 2);
            if (count($item) == 2)
                $headers[$item[0]] = trim($item[1]);
            else $headers[] = trim($value);
        }
        return $headers;
    }
}
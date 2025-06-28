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
     * @param string|null $strHeaders
     * @return array
     */
    public static function parseHeaders(?string $strHeaders): array
    {
        $headerArr = explode(PHP_EOL, $strHeaders);
        $headers = array();
        foreach ($headerArr as $header) {
            if (Utils::isEmpty($header)) continue;
            $item = explode(': ', $header, 2);
            if (count($item) != 2) $headers[] = trim($header);
            else $headers[$item[0]] = trim($item[1]);
        }
        return $headers;
    }
}
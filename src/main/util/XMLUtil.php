<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/9
 * Time: 18:28
 */

namespace lanlj\fw\util;

use stdClass;

class XMLUtil
{
    /**
     * 将xml内容转换为数组
     * @param string|null $xml xml文本
     * @return array
     */
    public static function toArray(?string $xml): array
    {
        if (is_null($xml)) return [];
        return is_array($arr = json_decode(self::parseXML($xml), true)) ? $arr : [];
    }

    /**
     * 解析XML
     * @param string $xml xml文本
     * @return string
     */
    private static function parseXML(string $xml): string
    {
        //考虑到xml文档中可能会包含<![CDATA[]]>标签，第三个参数设置为LIBXML_NOCDATA
        libxml_disable_entity_loader(true);
        $sxe = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
        return json_encode($sxe);
    }

    /**
     * 将xml内容转换为对象
     * @param string|null $xml xml文本
     * @return stdClass|null
     */
    public static function toObject(?string $xml): ?object
    {
        if (is_null($xml)) return NULL;
        return is_object($obj = json_decode(self::parseXML($xml))) ? $obj : NULL;
    }
}
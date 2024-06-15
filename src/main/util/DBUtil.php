<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/19
 * Time: 9:44
 */

namespace lanlj\fw\util;

use lanlj\fw\core\Strings;

final class DBUtil
{
    /**
     * 数据处理
     * @param object|array $data
     * @return array
     */
    public static function toDBArray($data): array
    {
        return !is_array($data) ? ArrayUtil::toArray($data, false, false, true) : $data;
    }

    /**
     * 转为指定class对象
     * @param object|array $data
     * @param string|null $class
     * @param bool $multi 是否为多维数组
     * @return object|array|null
     */
    public static function toClassObject($data, string $class = null, bool $multi = false)
    {
        if (!is_string($class)) return $data;
        if (!is_object($data) && !is_array($data)) return NULL;
        $objs = array();
        foreach ($multi ? $data : [$data] as $item) {
            $objs[] = BeanUtil::populate($item, $class, true);
        }
        return $multi ? $objs : $objs[0];
    }

    /**
     * 替换SQL语句占位符为实参
     * @param string $sql
     * @param mixed $parameters
     * @return string
     */
    public static function buildSQL(string $sql, ...$parameters): string
    {
        if (($size = count($parameters)) > 0) {
            if ($size == 1) $parameters = ArrayUtil::toArray($parameters[0], false, true);
            return preg_replace_callback(['/{([A-Za-z0-9_.]+)}/', '/:([A-Za-z0-9_.]+)/'], function ($matches) use ($parameters) {
                return ArrayUtil::getNestedValue($parameters, (new Strings($matches[1]))->split('.')) ?? $matches[0];
            }, $sql);
        }
        return $sql;
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/19
 * Time: 9:44
 */

namespace lanlj\fw\util;

use lanlj\fw\core\Strings;

class DBUtil
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
     * 转换为指定class对象
     * @param object|array $data
     * @param string|null $class
     * @param bool $multi 是否为多维数组
     * @return object|array|null
     */
    public static function toClassObject($data, ?string $class, bool $multi = false)
    {
        if (!is_string($class)) return $data;
        $objs = BeanUtil::populates($multi ? $data : [$data], $class, true);
        return $multi ? $objs : $objs[0];
    }

    /**
     * 预处理SQL语句
     * @param string $sql
     * @param mixed $parameters
     * @return string
     */
    public static function preparedSQL(string $sql, ...$parameters): string
    {
        if (($size = count($parameters)) > 0) {
            if ($size == 1) $parameters = ArrayUtil::toArray($parameters[0], false, true);
            preg_match_all('/\?[^=\d+]|\?$/', $sql, $matches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE);
            $count = 0;
            foreach ($matches as $k => $v) {
                $sql = substr_replace($sql, "?$k", $v[0][1] + ($count++), 1);
            }
            return preg_replace_callback([
                '/(\?)=?((\w+)\.[\w.]+)/', '/(\?)=?(\w+)/',
                '/(#)((\w+)\.[\w.]+)/', '/(#)(\w+)/', '/(#){((\w+)\.[\w.]+)}/', '/(#){(\w+)}/'
            ], function ($matches) use ($parameters) {
                if (!isset($matches[3])) $value = $parameters[$matches[2]];
                else $value = ArrayUtil::getNestedValue($parameters, (new Strings($matches[2]))->split('.'));
                if ($matches[1] == '#') return $value;
                if (is_null($value)) return 'NULL';
                if (is_string($value)) return "'$value'";
                return $value;
            }, $sql);
        }
        return $sql;
    }
}
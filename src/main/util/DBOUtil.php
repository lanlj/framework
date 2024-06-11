<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/19
 * Time: 9:44
 */

namespace lanlj\fw\util;

use ezsql\ezsqlModel;
use lanlj\fw\core\Strings;

class DBOUtil
{
    /**
     * @var ezsqlModel
     */
    protected ezsqlModel $dbo;

    /**
     * DBOUtil constructor.
     * @param ezsqlModel $dbo
     */
    public function __construct(ezsqlModel $dbo)
    {
        $this->dbo = $dbo;
    }

    /**
     * @param string $sql
     * @param mixed ...$parameters
     * @return string
     */
    public static function buildSQL(string $sql, ...$parameters): string
    {
        return self::buildSQL2($sql, $parameters);
    }

    /**
     * 根据参数长度匹配替换方法
     * @param string $sql
     * @param array $parameters
     * @return string
     */
    protected static function buildSQL2(string $sql, array $parameters): string
    {
        if (($size = count($parameters)) > 0) {
            if ($size == 1) $parameters = ArrayUtil::toArray($parameters[0], false, true);
            return preg_replace_callback(['/{([A-Za-z0-9_.]+)}/', '/:([A-Za-z0-9_.]+)/'], function ($matches) use ($parameters) {
                $value = ArrayUtil::getNestedValue($parameters, (new Strings($matches[1]))->split('.'));
                return str_replace($matches[0], $value, $matches[0]);
            }, $sql);
        }
        return $sql;
    }

    /**
     * @return ezsqlModel
     */
    public function getDBO(): ezsqlModel
    {
        return $this->dbo;
    }

    /**
     * @param ezsqlModel $dbo
     */
    public function setDBO(ezsqlModel $dbo)
    {
        $this->dbo = $dbo;
    }

    /**
     * 插入条目
     * @param string $table
     * @param object|array $data
     * @return bool
     */
    public function insert(string $table, $data): bool
    {
        return $this->dbo->insert(
            $table, !is_array($data) ? ArrayUtil::toArray($data, false, false, true) : $data
        );
    }

    /**
     * 查询集合
     * @param string $sql
     * @param string $class
     * @param mixed ...$parameters
     * @return array
     */
    public function getList(string $sql, string $class = null, ...$parameters): ?array
    {
        $rst = $this->dbo->get_results(self::buildSQL2($sql, $parameters));
        if (!is_string($class) || !is_array($rst)) return $rst;
        $objs = array();
        foreach ($rst as $item) {
            $objs[] = BeanUtil::populate($item, $class, true);
        }
        return $objs;
    }

    /**
     * 查询单个
     * @param string $sql
     * @param string $class
     * @param mixed ...$parameters
     * @return object|null
     */
    public function getOne(string $sql, string $class = null, ...$parameters): ?object
    {
        $rst = $this->dbo->get_row(self::buildSQL2($sql, $parameters));
        if (!is_string($class) || is_null($rst)) return $rst;
        return BeanUtil::populate($rst, $class, true);
    }

    /**
     * 执行SQL
     * @param string $sql
     * @param mixed ...$parameters
     * @return bool
     */
    public function query(string $sql, ...$parameters): bool
    {
        return $this->dbo->query(self::buildSQL2($sql, $parameters));
    }
}
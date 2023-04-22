<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/19
 * Time: 9:44
 */

namespace lanlj\fw\util;

use ezSQLcore;

final class DBOUtil
{
    /**
     * @var ezSQLcore
     */
    private $dbo;

    /**
     * DBOUtil constructor.
     * @param ezSQLcore $dbo
     */
    public function __construct(ezSQLcore $dbo)
    {
        $this->dbo = $dbo;
    }

    /**
     * @param $sql
     * @param array ...$parameters
     * @return string
     */
    public static function buildSQL($sql, ...$parameters)
    {
        return self::buildSQL2($sql, $parameters);
    }

    /**
     * 根据参数长度匹配替换方法
     * @param string $sql
     * @param array $parameters
     * @return string
     */
    private static function buildSQL2($sql, array $parameters)
    {
        if (($size = count($parameters)) > 0) {
            if ($size == 1) $parameters = ArrayUtil::toArray($parameters[0], false, true);
            return preg_replace_callback('/{([A-Za-z0-9_]+)}|:([A-Za-z0-9_]+)/', function ($matches) use ($parameters) {
                $i = isset($matches[2]) ? $matches[2] : $matches[1];
                return str_replace($matches[0], isset($parameters[$i]) ? $parameters[$i] : null, $matches[0]);
            }, $sql);
        }
        return $sql;
    }

    /**
     * @return ezSQLcore
     */
    public function getDBO()
    {
        return $this->dbo;
    }

    /**
     * @param ezSQLcore $dbo
     */
    public function setDBO(ezSQLcore $dbo)
    {
        $this->dbo = $dbo;
    }

    /**
     * 查询集合
     * @param string $sql
     * @param string $class
     * @param array ...$parameters
     * @return array
     */
    public function getList($sql, $class = null, ...$parameters)
    {
        $rst = $this->dbo->get_results(self::buildSQL2($sql, $parameters));
        if (!is_string($class) || count($rst) < 1)
            return $rst;
        $objs = [];
        foreach ($rst as $item) {
            $objs[] = BeanUtil::populate($item, $class, true);
        }
        return $objs;
    }

    /**
     * 查询单个
     * @param string $sql
     * @param string $class
     * @param array ...$parameters
     * @return object|null
     */
    public function getOne($sql, $class = null, ...$parameters)
    {
        $rst = $this->dbo->get_row(self::buildSQL2($sql, $parameters));
        if (!is_string($class) || is_null($rst))
            return $rst;
        return BeanUtil::populate($rst, $class, true);
    }

    /**
     * 执行SQL
     * @param string $sql
     * @param array ...$parameters
     * @return bool
     */
    public function query($sql, ...$parameters)
    {
        return $this->dbo->query(self::buildSQL2($sql, $parameters));
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: LanLiJun
 * Mail: jun@lanlj.com
 * Date: 2024/6/12
 * Time: 19:18
 */

namespace lanlj\fw\dao;

use ezsql\ezsqlModel;
use lanlj\fw\util\DBUtil;
use function ezsql\functions\limit;

class DAO
{
    /**
     * @var string
     */
    protected string $table;

    /**
     * @var ezsqlModel
     */
    protected $dbo;

    /**
     * DAO constructor.
     * @param string $table
     * @param ezsqlModel $dbo
     */
    public function __construct(string $table, $dbo)
    {
        $this->table = $table;
        $this->dbo = $dbo;
    }

    /**
     * 插入条目
     * @param object|array $data
     * @return bool
     */
    public function insert($data): bool
    {
        return $this->dbo->insert($this->table, DBUtil::toDBArray($data));
    }

    /**
     * 更新条目
     * @param object|array $data
     * @param mixed ...$whereConditions
     * @return bool
     */
    public function update($data, ...$whereConditions): bool
    {
        return $this->dbo->update($this->table, DBUtil::toDBArray($data), ...$whereConditions);
    }

    /**
     * 删除条目
     * @param mixed ...$whereConditions
     * @return bool
     */
    public function delete(...$whereConditions): bool
    {
        return $this->dbo->delete($this->table, ...$whereConditions);
    }

    /**
     * 查询单个条目
     * @param string $columnFields
     * @param string|null $class
     * @param mixed ...$conditions
     * @return object|null
     */
    public function select(string $columnFields = '*', string $class = null, ...$conditions): ?object
    {
        $conditions[] = limit(1);
        return $this->selectALL($columnFields, $class, ...$conditions)[0];
    }

    /**
     * 查询所有条目
     * @param string $columnFields
     * @param string|null $class
     * @param mixed ...$conditions
     * @return array|null
     */
    public function selectALL(string $columnFields = '*', string $class = null, ...$conditions): ?array
    {
        $results = $this->dbo->select($this->table, $columnFields, ...$conditions);
        if (!is_array($results)) return null;
        return DBUtil::toClassObject($results, $class, true);
    }

    /**
     * 执行SQL
     * @param string $sql
     * @param mixed ...$parameters
     * @return bool
     */
    public function query(string $sql, ...$parameters): bool
    {
        return $this->dbo->query(DBUtil::preparedSQL($sql, ...$parameters));
    }

    /**
     * 通过SQL查询单个
     * @param string $sql
     * @param string|null $class
     * @param mixed ...$parameters
     * @return object|null
     */
    public function getOne(string $sql, string $class = null, ...$parameters): ?object
    {
        return DBUtil::toClassObject($this->dbo->get_row(DBUtil::preparedSQL($sql, ...$parameters)), $class);
    }

    /**
     * 通过SQL查询集合
     * @param string $sql
     * @param string|null $class
     * @param mixed ...$parameters
     * @return array|null
     */
    public function getList(string $sql, string $class = null, ...$parameters): ?array
    {
        $results = $this->dbo->get_results(DBUtil::preparedSQL($sql, ...$parameters));
        if (!is_array($results)) return null;
        return DBUtil::toClassObject($results, $class, true);
    }

    /**
     * 通过SQL查询单列
     * @param string $sql
     * @param int $col 列索引，0=第1列
     * @param mixed ...$parameters
     * @return array
     */
    public function getCol(string $sql, int $col = 0, ...$parameters): array
    {
        return $this->dbo->get_col(DBUtil::preparedSQL($sql, ...$parameters), $col);
    }

    /**
     * 通过SQL查询单值
     * @param string $sql
     * @param int $col 列索引，0=第1列
     * @param int $row 行索引，0=第1行
     * @param mixed ...$parameters
     * @return mixed|null
     */
    public function getVar(string $sql, int $col = 0, int $row = 0, ...$parameters)
    {
        return $this->dbo->get_var(DBUtil::preparedSQL($sql, ...$parameters), $col, $row);
    }
}
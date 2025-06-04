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
     * @var string|null
     */
    private ?string $castClass;

    /**
     * @var string|null
     */
    private ?string $castClassBak = null;

    /**
     * DAO constructor.
     * @param string $table
     * @param ezsqlModel $dbo
     * @param string|null $castClass
     */
    public function __construct(string $table, $dbo, string $castClass = null)
    {
        $this->table = $table;
        $this->dbo = $dbo;
        $this->castClass = $castClass;
        $this->castClassBak = $castClass;
    }

    /**
     * 转换为指定class对象
     * @param string|null $castClass 转换的class名称
     * @return DAO
     */
    public function setCastClass(string $castClass = null): DAO
    {
        if (!is_null($this->castClass)) $this->castClassBak = $this->castClass;
        $this->castClass = $castClass;
        return $this;
    }

    /**
     * 恢复为上一次指定转换的class名称
     * @return DAO
     */
    public function restoreCastClass(): DAO
    {
        $this->castClass = $this->castClassBak;
        return $this;
    }

    /**
     * 插入条目
     * @param object|array $data
     * @return int
     */
    public function insert($data): int
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
     * @param mixed ...$conditions
     * @return object|null
     */
    public function select(string $columnFields = '*', ...$conditions): ?object
    {
        $conditions[] = limit(1);
        return $this->selectALL($columnFields, ...$conditions)[0];
    }

    /**
     * 查询所有条目
     * @param string $columnFields
     * @param mixed ...$conditions
     * @return array|null
     */
    public function selectALL(string $columnFields = '*', ...$conditions): ?array
    {
        $results = $this->dbo->select($this->table, $columnFields, ...$conditions);
        if (!is_array($results)) return null;
        return DBUtil::toClassObject($results, $this->castClass, true);
    }

    /**
     * 执行SQL
     * @param string $sql
     * @param mixed ...$parameters
     * @return bool|mixed
     */
    public function query(string $sql, ...$parameters)
    {
        return $this->dbo->query(DBUtil::preparedSQL($sql, ...$parameters));
    }

    /**
     * 通过SQL查询单个
     * @param string $sql
     * @param mixed ...$parameters
     * @return object|null
     */
    public function getOne(string $sql, ...$parameters): ?object
    {
        return DBUtil::toClassObject($this->dbo->get_row(DBUtil::preparedSQL($sql, ...$parameters)), $this->castClass);
    }

    /**
     * 通过SQL查询集合
     * @param string $sql
     * @param mixed ...$parameters
     * @return array|null
     */
    public function getList(string $sql, ...$parameters): ?array
    {
        $results = $this->dbo->get_results(DBUtil::preparedSQL($sql, ...$parameters));
        if (!is_array($results)) return null;
        return DBUtil::toClassObject($results, $this->castClass, true);
    }

    /**
     * 通过SQL查询单列
     * @param string $sql
     * @param int $col 列索引，0=第1列
     * @param mixed ...$parameters
     * @return array|null
     */
    public function getCol(string $sql, int $col = 0, ...$parameters): ?array
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
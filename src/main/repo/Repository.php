<?php
/**
 * Created by PhpStorm.
 * User: LanLiJun
 * Mail: jun@lanlj.com
 * Date: 2024/6/9
 * Time: 14:23
 */

namespace lanlj\fw\repo;

use ezsql\ezsqlModel;
use lanlj\fw\dao\DAO;

abstract class Repository
{
    /**
     * @var ezsqlModel
     */
    protected static $dbo;

    /**
     * @var DAO
     */
    private DAO $dao;

    /**
     * @var string|null
     */
    private ?string $class;

    /**
     * Repository constructor.
     */
    public function __construct()
    {
        $this->initDAO();
    }

    /**
     * 初始化Dao对象
     * 实现此方法后调用 @method setDAO(string $table)
     * 构造器会继承调用此方法 @method initDAO()
     */
    protected abstract function initDAO(): void;

    /**
     * @param ezsqlModel $dbo
     */
    public static function initialize($dbo)
    {
        self::$dbo = $dbo;
        self::$dbo->prepareOn();
        self::$dbo->hide_errors();
    }

    /**
     * 插入条目
     * @param object|array $data
     * @return bool
     */
    public function insert($data): bool
    {
        return $this->dao->insert($data);
    }

    /**
     * 更新条目
     * @param object|array $data
     * @param mixed ...$whereConditions
     * @return bool
     */
    public function update($data, ...$whereConditions): bool
    {
        return $this->dao->update($data, ...$whereConditions);
    }

    /**
     * 删除条目
     * @param mixed ...$whereConditions
     * @return bool
     */
    public function delete(...$whereConditions): bool
    {
        return $this->dao->delete(...$whereConditions);
    }

    /**
     * 查询单个条目
     * @param string $columnFields
     * @param mixed ...$conditions
     * @return object|null
     */
    public function select(string $columnFields = '*', ...$conditions): ?object
    {
        return $this->dao->select($columnFields, $this->class, ...$conditions);
    }

    /**
     * 查询所有条目
     * @param string $columnFields
     * @param mixed ...$conditions
     * @return array|null
     */
    public function selectALL(string $columnFields = '*', ...$conditions): ?array
    {
        return $this->dao->selectALL($columnFields, $this->class, ...$conditions);
    }

    /**
     * 执行SQL
     * @param string $sql
     * @param mixed ...$parameters
     * @return bool
     */
    public function query(string $sql, ...$parameters): bool
    {
        return $this->dao->query($sql, ...$parameters);
    }

    /**
     * 通过SQL查询单个
     * @param string $sql
     * @param mixed ...$parameters
     * @return object|null
     */
    public function getOne(string $sql, ...$parameters): ?object
    {
        return $this->dao->getOne($sql, $this->class, ...$parameters);
    }

    /**
     * 通过SQL查询集合
     * @param string $sql
     * @param mixed ...$parameters
     * @return array|null
     */
    public function getList(string $sql, ...$parameters): ?array
    {
        return $this->dao->getList($sql, $this->class, ...$parameters);
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
        return $this->dao->getCol($sql, $col, ...$parameters);
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
        return $this->dao->getVar($sql, $col, $row, ...$parameters);
    }

    /**
     * @param string $table 表名
     */
    protected function setDAO(string $table): void
    {
        $this->dao = new DAO($table, self::$dbo);
    }

    /**
     * @param string|null $class 实体类class
     * @return Repository
     */
    protected function setClass(string $class = null): Repository
    {
        $this->class = $class;
        return $this;
    }
}
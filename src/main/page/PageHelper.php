<?php
/**
 * Created by PhpStorm.
 * User: LanLiJun
 * Mail: jun@lanlj.com
 * Date: 2025/6/26
 * Time: 13:54
 */

namespace lanlj\fw\page;

use lanlj\fw\core\Strings;
use lanlj\fw\repo\Repository;
use lanlj\fw\util\BooleanUtil;
use function ezsql\functions\limit;

class PageHelper
{
    /**
     * @var Repository
     */
    private Repository $repository;

    /**
     * 是否可分页
     * @var bool
     */
    private bool $pageable = false;

    /**
     * 开始下标
     * @var int|null
     */
    private ?int $offset;

    /**
     * 每页个数
     * @var int|null
     */
    private ?int $pageSize;

    /**
     * @var PageInfo
     */
    private PageInfo $pageInfo;

    /**
     * @var array
     */
    private array $conditions = [];

    /**
     * @var array
     */
    private array $parameters = [];

    /**
     * @param Repository $repository
     */
    public function __construct(Repository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * 开启分页
     * @param int|null $pageNum
     * @param int|null $pageSize
     * @return bool
     */
    public function startPage(?int $pageNum, ?int $pageSize): bool
    {
        $this->pageable = false;
        if (BooleanUtil::all(!is_null($pageNum), !is_null($pageSize), $pageSize > 0)) {
            $this->pageable = true;
            $this->pageSize = $pageSize;
            $this->offset = $pageNum < 1 ? 0 : ($pageNum - 1) * $pageSize;
            $this->pageInfo = (new PageInfo($pageNum, $pageSize))
                ->setStartRow($this->offset + 1)->setEndRow($this->offset + $pageSize);
        }
        return $this->pageable;
    }

    /**
     * @param ...$conditions
     * @return array
     */
    public function getConditions(...$conditions): array
    {
        if (!$this->pageable) return $conditions;
        $this->conditions = $conditions;
        $conditions[] = limit($this->pageSize, $this->offset);
        return $conditions;
    }

    /**
     * @param array $parameters
     * @return $this
     */
    public function setParameters(...$parameters): self
    {
        $this->parameters = $parameters;
        return $this;
    }

    /**
     * @param array|object|null $list
     * @param string|null $parts
     * @param string $columnField
     * @return array|object|PageInfo|null
     */
    public function make($list, string $parts = null, string $columnField = '*')
    {
        if (!$this->pageable) return $list;
        if (!is_null($list)) $this->pageInfo->setList($list);
        if (is_null($parts)) $total = $this->repository->getCount($columnField, $this->conditions, ...$this->parameters);
        else {
            $sql = new Strings("SELECT COUNT($columnField) FROM $parts ");
            $sql->concat(implode(' ', $this->conditions));
            $total = $this->repository->getVar($sql->getString(), 0, 0, ...$this->parameters);
        }
        $this->pageInfo->setTotal($total)->setPages(ceil($total / $this->pageSize));
        return $this->pageInfo;
    }

    /**
     * @return bool
     */
    public function isPageable(): bool
    {
        return $this->pageable;
    }
}
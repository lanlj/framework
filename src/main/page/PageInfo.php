<?php
/**
 * Created by PhpStorm.
 * User: LanLiJun
 * Mail: jun@lanlj.com
 * Date: 2025/6/26
 * Time: 13:55
 */

namespace lanlj\fw\page;

use lanlj\fw\bean\BeanMapping;
use lanlj\fw\core\Arrays;

class PageInfo implements BeanMapping
{
    /**
     * 当前页码
     * @var int
     */
    private int $pageNum;

    /**
     * 每页个数
     * @var int
     */
    private int $pageSize;

    /**
     * 当前页个数
     * @var int
     */
    private int $size = 0;

    /**
     * 由第几条开始
     * @var int|null
     */
    private ?int $startRow;

    /**
     * 到第几条结束
     * @var int|null
     */
    private ?int $endRow;

    /**
     * 总条数
     * @var int
     */
    private int $total = 0;

    /**
     * 总页数
     * @var int
     */
    private int $pages = 0;

    /**
     * 数据集合
     * @var array|object
     */
    private $list = [];

    /**
     * 上一页
     * @var int|null
     */
    private ?int $prePage;

    /**
     * 下一页
     * @var int|null
     */
    private ?int $nextPage;

    /**
     * 是否为首页
     * @var bool|null
     */
    private ?bool $isFirstPage;

    /**
     * 是否为尾页
     * @var bool|null
     */
    private ?bool $isLastPage;

    /**
     * 是否有上一页
     * @var bool|null
     */
    private ?bool $hasPreviousPage;

    /**
     * 是否有下一页
     * @var bool|null
     */
    private ?bool $hasNextPage;

    /**
     * 每页显示的页码个数
     * @var int|null
     */
    private ?int $navigatePages;

    /**
     * 首页
     * @var int|null
     */
    private ?int $navigateFirstPage;

    /**
     * 尾页
     * @var int|null
     */
    private ?int $navigateLastPage;

    /**
     * 页码数
     * @var array|null
     */
    private ?array $navigatePageNums;

    /**
     * @param int $pageNum
     * @param int $pageSize
     */
    public function __construct(int $pageNum, int $pageSize)
    {
        $this->pageNum = $pageNum;
        $this->pageSize = $pageSize;
    }

    /**
     * @inheritDoc
     */
    public static function mapping($args): self
    {
        if ($args instanceof self) return $args;
        $args = new Arrays($args);
        return (new self(
            $args->get('pageNum'),
            $args->get('pageSize')
        ))->setSize($args->get('size'))
            ->setStartRow($args->get('startRow'))
            ->setEndRow($args->get('endRow'))
            ->setTotal($args->get('total'))
            ->setPages($args->get('pages'))
            ->setList($args->get('list'))
            ->setPrePage($args->get('prePage'))
            ->setNextPage($args->get('nextPage'))
            ->setIsFirstPage($args->get('isFirstPage'))
            ->setIsLastPage($args->get('isLastPage'))
            ->setHasPreviousPage($args->get('hasPreviousPage'))
            ->setHasNextPage($args->get('hasNextPage'))
            ->setNavigatePages($args->get('navigatePages'))
            ->setNavigateFirstPage($args->get('navigateFirstPage'))
            ->setNavigateLastPage($args->get('navigateLastPage'))
            ->setNavigatePageNums($args->get('navigatePageNums'));
    }

    /**
     * @return int
     */
    public function getPageNum(): int
    {
        return $this->pageNum;
    }

    /**
     * @param int $pageNum
     * @return $this
     */
    public function setPageNum(int $pageNum): self
    {
        $this->pageNum = $pageNum;
        return $this;
    }

    /**
     * @return int
     */
    public function getPageSize(): int
    {
        return $this->pageSize;
    }

    /**
     * @param int $pageSize
     * @return $this
     */
    public function setPageSize(int $pageSize): self
    {
        $this->pageSize = $pageSize;
        return $this;
    }

    /**
     * @return int
     */
    public function getSize(): int
    {
        return $this->size;
    }

    /**
     * @param int $size
     * @return $this
     */
    public function setSize(int $size): self
    {
        $this->size = $size;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getStartRow(): ?int
    {
        return $this->startRow;
    }

    /**
     * @param int|null $startRow
     * @return $this
     */
    public function setStartRow(?int $startRow): self
    {
        $this->startRow = $startRow;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getEndRow(): ?int
    {
        return $this->endRow;
    }

    /**
     * @param int|null $endRow
     * @return $this
     */
    public function setEndRow(?int $endRow): self
    {
        $this->endRow = $endRow;
        return $this;
    }

    /**
     * @return int
     */
    public function getTotal(): int
    {
        return $this->total;
    }

    /**
     * @param int $total
     * @return $this
     */
    public function setTotal(int $total): self
    {
        $this->total = $total;
        return $this;
    }

    /**
     * @return int
     */
    public function getPages(): int
    {
        return $this->pages;
    }

    /**
     * @param int $pages
     * @return $this
     */
    public function setPages(int $pages): self
    {
        $this->pages = $pages;
        return $this;
    }

    /**
     * @return array|object
     */
    public function getList()
    {
        return $this->list;
    }

    /**
     * @param array|object $list
     * @return $this
     */
    public function setList($list): self
    {
        if (is_array($list)) {
            $this->list = $list;
            $this->setSize(count($list));
        }
        if (is_object($list)) $this->list = $list;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getPrePage(): ?int
    {
        return $this->prePage;
    }

    /**
     * @param int|null $prePage
     * @return $this
     */
    public function setPrePage(?int $prePage): self
    {
        $this->prePage = $prePage;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getNextPage(): ?int
    {
        return $this->nextPage;
    }

    /**
     * @param int|null $nextPage
     * @return $this
     */
    public function setNextPage(?int $nextPage): self
    {
        $this->nextPage = $nextPage;
        return $this;
    }

    /**
     * @return bool|null
     */
    public function getIsFirstPage(): ?bool
    {
        return $this->isFirstPage;
    }

    /**
     * @param bool|null $isFirstPage
     * @return $this
     */
    public function setIsFirstPage(?bool $isFirstPage): self
    {
        $this->isFirstPage = $isFirstPage;
        return $this;
    }

    /**
     * @return bool|null
     */
    public function getIsLastPage(): ?bool
    {
        return $this->isLastPage;
    }

    /**
     * @param bool|null $isLastPage
     * @return $this
     */
    public function setIsLastPage(?bool $isLastPage): self
    {
        $this->isLastPage = $isLastPage;
        return $this;
    }

    /**
     * @return bool|null
     */
    public function getHasPreviousPage(): ?bool
    {
        return $this->hasPreviousPage;
    }

    /**
     * @param bool|null $hasPreviousPage
     * @return $this
     */
    public function setHasPreviousPage(?bool $hasPreviousPage): self
    {
        $this->hasPreviousPage = $hasPreviousPage;
        return $this;
    }

    /**
     * @return bool|null
     */
    public function getHasNextPage(): ?bool
    {
        return $this->hasNextPage;
    }

    /**
     * @param bool|null $hasNextPage
     * @return $this
     */
    public function setHasNextPage(?bool $hasNextPage): self
    {
        $this->hasNextPage = $hasNextPage;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getNavigatePages(): ?int
    {
        return $this->navigatePages;
    }

    /**
     * @param int|null $navigatePages
     * @return $this
     */
    public function setNavigatePages(?int $navigatePages): self
    {
        $this->navigatePages = $navigatePages;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getNavigateFirstPage(): ?int
    {
        return $this->navigateFirstPage;
    }

    /**
     * @param int|null $navigateFirstPage
     * @return $this
     */
    public function setNavigateFirstPage(?int $navigateFirstPage): self
    {
        $this->navigateFirstPage = $navigateFirstPage;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getNavigateLastPage(): ?int
    {
        return $this->navigateLastPage;
    }

    /**
     * @param int|null $navigateLastPage
     * @return $this
     */
    public function setNavigateLastPage(?int $navigateLastPage): self
    {
        $this->navigateLastPage = $navigateLastPage;
        return $this;
    }

    /**
     * @return array|null
     */
    public function getNavigatePageNums(): ?array
    {
        return $this->navigatePageNums;
    }

    /**
     * @param array|null $navigatePageNums
     * @return $this
     */
    public function setNavigatePageNums(?array $navigatePageNums): self
    {
        $this->navigatePageNums = $navigatePageNums;
        return $this;
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: LanLiJun
 * Mail: jun@lanlj.com
 * Date: 2025/6/26
 * Time: 3:48
 */

namespace lanlj\fw\model;

class PageModel extends JsonModel
{
    /**
     * 当前页码
     * @var int|null
     */
    private ?int $pageNum;

    /**
     * 每页个数
     * @var int|null
     */
    private ?int $pageSize;

    /**
     * 当前页个数
     * @var int|null
     */
    private ?int $size;

    /**
     * 总条数
     * @var int|null
     */
    private ?int $total;

    /**
     * 总页数
     * @var int|null
     */
    private ?int $pages;

    /**
     * @return int|null
     */
    public function getPageNum(): ?int
    {
        return $this->pageNum;
    }

    /**
     * @param int|null $pageNum
     * @return $this
     */
    public function setPageNum(?int $pageNum): self
    {
        $this->pageNum = $pageNum;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getPageSize(): ?int
    {
        return $this->pageSize;
    }

    /**
     * @param int|null $pageSize
     * @return $this
     */
    public function setPageSize(?int $pageSize): self
    {
        $this->pageSize = $pageSize;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getSize(): ?int
    {
        return $this->size;
    }

    /**
     * @param int|null $size
     * @return $this
     */
    public function setSize(?int $size): self
    {
        $this->size = $size;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getTotal(): ?int
    {
        return $this->total;
    }

    /**
     * @param int|null $total
     * @return $this
     */
    public function setTotal(?int $total): self
    {
        $this->total = $total;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getPages(): ?int
    {
        return $this->pages;
    }

    /**
     * @param int|null $pages
     * @return $this
     */
    public function setPages(?int $pages): self
    {
        $this->pages = $pages;
        return $this;
    }
}
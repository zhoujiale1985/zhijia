<?php
/**
 * This is Paginator handle.
 * User: nintenichi
 * Date: 2019-08-06 11:53
 * Email: <rentianyi@homme-inc.com>
 */

namespace ZhijiaCommon\Component;

/**
 * Trait Paginator
 * 分页处理 Trait
 *
 * @package ZhijiaCommon\Component
 */
trait Paginator
{
    /**
     * 当前页码
     *
     * @var int
     */
    private $currentPage = 0;

    /**
     * 每页条数
     *
     * @var int
     */
    private $pageSize = 20;

    /**
     * 跳过数据值
     *
     * @var int
     */
    private $skipDataValue = 0;

    /**
     * 总页数
     *
     * @var int
     */
    private $totalPages = 0;

    /**
     * 数据总条数
     * @var int
     */
    private $totalCount = 0;

    /**
     * Paginator Handle.
     * 分页程序
     *
     * @param $currentPage int
     * @param $pageSize int
     * @param $totalCount int
     */
    public function Paginator($currentPage, $pageSize, $totalCount) : void
    {
        $this->currentPage   = $currentPage;
        $this->pageSize      = $pageSize;
        $this->totalCount    = $totalCount;
        $this->totalPages    = ceil($totalCount / $pageSize);
        $this->skipDataValue = $currentPage * $pageSize;
        //判断如果当前页值超过最大页数的值，则展示最后一页的数据
        if ($currentPage > $this->totalPages) {
            $this->skipDataValue = ($this->totalPages - 1) * $pageSize;
        }
    }

    /**
     * Get Skip data value.
     * 获取跳过数据值
     *
     * @return int
     */
    public function getSkipDataValue() : int
    {
        return $this->skipDataValue;
    }

    /**
     * Get Total pages.
     * 获取页面信息
     *
     * @return array
     */
    public function getPageInfo() : array
    {
        return [
            'count'        => $this->totalCount,
            'current_page' => $this->currentPage,
            'page_size'    => $this->pageSize,
            'total_pages' => $this->totalPages,
        ];
    }
}

<?php
/*******************************************************************************
 * Copyright (c) 2021. CleanPHP. All Rights Reserved.
 ******************************************************************************/

namespace app\core\database\sql;
/**
 * Class Page
 * Created By ankio.
 * Date : 2022/1/12
 * Time : 7:43 下午
 * Description : 分页处理类
 */
class Page
{
    private array $page;

    public function __construct($page)
    {
        $this->page=$page;
    }
    /**
     * 总数量
     * @return int
     */
    public function getTotalCount(): int
    {
        return $this->page["total_count"]??0;
    }

    /**
     * 一页大小
     * @return int
     */
    public function getPageSize(): int
    {
        return $this->page["page_size"]??0;
    }

    /**
     * 总页数
     * @return int
     */
    public function getTotalPage(): int
    {
        return $this->page["total_page"]??0;
    }

    /**
     * 第一页
     * @return int
     */
    public function getFirstPage(): int
    {
        return $this->page["first_page"]??0;
    }

    /**
     * 上一页
     * @return int
     */
    public function getPrevPage(): int
    {
        return $this->page["prev_page"]??0;
    }

    /**
     * 上一页
     * @return int
     */
    public function getNextPage(): int
    {
        return $this->page["next_page"]??0;
    }

    /**
     * 最后一页
     * @return int
     */
    public function getLastPage(): int
    {
        return $this->page["last_page"]??0;
    }

    /**
     * 当前页
     * @return int
     */
    public function getCurrentPage(): int
    {
        return $this->page["current_page"]??0;
    }

    /**
     * 所有页
     * @return int
     */
    public function getAllPages(): int
    {
        return $this->page["all_pages"]??0;
    }

    /**
     * 偏移
     * @return int
     */
    public function getOffset(): int
    {
        return $this->page["offset"]??0;
    }

    /**
     *  一页大小
     * @return int
     */
    public function getLimit(): int
    {
        return $this->page["limit"]??0;
    }
}
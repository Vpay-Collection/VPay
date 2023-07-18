<?php
/*
 * Copyright (c) 2023. Ankio.  由CleanPHP4强力驱动。
 */

/**
 * Package: library\database\operation
 * Class SelectOperation
 * Created By ankio.
 * Date : 2022/11/16
 * Time : 18:18
 * Description :
 */

namespace library\database\operation;

use Exception;
use library\database\Db;
use library\database\exception\DbFieldError;
use library\database\object\Field;
use library\database\object\Page;

class SelectOperation extends BaseOperation
{
    const SORT_DESC = "DESC";
    const SORT_ASC = "ASC";
    /**
     * @var Page|null
     */
    private ?Page $page;//开启分页的分页数据

    private bool $cache = true;

    /**
     * 是否禁止使用缓存
     * @return $this
     */
    public function noCache($is_nocache = true): SelectOperation
    {
        $this->cache = $is_nocache;
        return $this;
    }

    /**
     * 初始化
     * @param mixed ...$field 需要的字段
     */
    public function __construct(Db &$db, $m, ...$field)
    {
        parent::__construct($db, $m);
        $this->opt = [];
        $this->opt['type'] = 'select';
        $this->opt['distinct'] = '';
        $this->opt['field'] = (isset($field[0]) && $field[0] instanceof Field) ? $field[0]->toString() : (new Field(...$field))->toString();
        $this->bind_param = [];
    }


    /**
     * 使用排序
     * @param string $string 排序方式
     * @return $this
     * @throws DbFieldError
     */
    public function orderBy(string $string, string $type = self::SORT_DESC): SelectOperation
    {
        if (!Field::isName($string)) {
            throw new DbFieldError("字段名称只允许为字母、点、下划线", $string);
        }
        if (!in_array($type, [self::SORT_DESC, self::SORT_ASC])) {
            $type = self::SORT_DESC;
        }
        if (isset($this->opt['order']) && $this->opt['order'] !== "") {
            $this->opt['order'] = $this->opt['order'] . "," . $string . " " . $type;
        } else {
            $this->opt['order'] = $string . " " . $type;
        }

        return $this;
    }


    /**
     * 按照某个字段分组
     * @param string $string
     * @return $this
     * @throws DbFieldError
     */
    public function groupBy(string $string): SelectOperation
    {
        if (!Field::isName($string))
            throw new DbFieldError("字段名称只允许为字母、点、下划线", $string);
        $this->opt['group_by'] = $string;
        return $this;
    }


    /**
     * limit函数
     * @param int $start limit开始
     * @param int $end limit结束
     * @return $this
     */
    public function limit(int $start = 1, int $end = -1): SelectOperation
    {
        unset($this->opt['page']);
        $limit = strval($start);
        if ($end != -1) $limit .= "," . $end;
        $this->opt['limit'] = $limit;
        return $this;
    }

    /**
     * 分页
     * @param int $start 开始
     * @param int $count 数量
     * @param int $range 最多分页
     * @return $this
     */
    public function page(int $start = 1, int $count = 10, int $range = 10, &$page = null): SelectOperation
    {
        unset($this->opt['limit']);
        $this->opt['page'] = true;
        $this->opt['start'] = $start;
        $this->opt['count'] = $count;
        $this->opt['range'] = $range;
        $this->page = &$page;
        return $this;
    }

    /**
     * 提交
     * @return array|int
     */
    public function commit($object = true)
    {

        if($object && str_contains($this->opt["table_name"],",")){
            $object = false;
        }

        if (isset($this->opt['start']) && isset($this->opt['count']) && isset($this->opt['range'])) {
            $sql = 'SELECT COUNT(*) as M_COUNTER ';
            $sql .= $this->getOpt('FROM', 'table_name');
            $sql .= $this->getOpt('WHERE', 'where');
            $sql .= $this->getOpt('ORDER BY', 'order');
            $sql .= $this->getOpt('GROUP BY', 'groupBy');

            try {
                $total = $this->db->execute($sql, $this->bind_param, true)[0]['M_COUNTER'];
            } catch (Exception $e) {
                $total = 0;

            }


            if (!isset($this->opt['start'])) {
                $this->opt['start'] = 0;
                $this->opt['count'] = 10;
                $this->opt['range'] = 10;
            }
            $page = $this->pager($this->opt['start'], $this->opt['count'], $this->opt['range'], $total);

            if (!empty($page)) {
                if ($page['offset'] < 0) {
                    $page['offset'] = 0;
                }
                $this->opt['limit'] = $page['offset'] . ',' . $page['limit'];
            }

            $this->page = new Page($page);
        }

        $result = parent::__commit(true,$this->cache);
        if ($object && $this->model !== null) {
            return $this->translate2Model($this->model, $result);
        } else {
            return $result;
        }

    }

    /**
     * 分页函数
     * @param int $page 起始页
     * @param int $page_size 一页的数量
     * @param int $scope 最多分页数量
     * @param int $total 总量
     * @return array|null
     */
    private function pager(int $page, int $page_size = 10, int $scope = 10, int $total = 0): ?array
    {
        $page_array = [
            'total_count' => $total,//总数量
            'page_size' => $page_size,//一页大小
            'total_page' => 1,//总页数
            'first_page' => 1,//第一页
            'prev_page' => ((1 == $page) ? 1 : ($page - 1)),//上一页
            'next_page' => (($page == 1) ? 1 : ($page + 1)),//下一页
            'last_page' => 1,//最后一页
            'current_page' => $page,//当前页
            'all_pages' => [],//所有页
            'offset' => ($page - 1) * $page_size,
            'limit' => $page_size,
        ];
        if ($total > $page_size) {
            $total_page = ceil($total / $page_size);
            $page = min(intval(max($page, 1)), $total_page);
            $page_array["total_page"] = $total_page;
            $page_array["next_page"] = (($page == $total_page) ? $total_page : ($page + 1));//下一页
            $page_array["last_page"] = $total_page;

            if ($total_page <= $scope) {
                $page_array['all_pages'] = range(1, $total_page);
            } elseif ($page <= $scope / 2) {
                $page_array['all_pages'] = range(1, $scope);
            } elseif ($page <= $total_page - $scope / 2) {
                $right = $page + (int)($scope / 2);
                $page_array['all_pages'] = range($right - $scope + 1, $right);
            } else {
                $page_array['all_pages'] = range($total_page - $scope + 1, $total_page);
            }
        }
        return $page_array;
    }

    /**
     * 统计查出来的数据的总数
     * @param array $conditions 统计条件
     * @return int|mixed
     */
    public function count(array $conditions): mixed
    {
        if (!empty($conditions)) $this->where($conditions);
        $sql = /** @lang text */
            "SELECT COUNT(*) AS M_COUNTER FROM " . $this->opt['table_name'] . "  " . (empty($conditions) ? '' : 'where ' . $this->opt['where']);
        $this->tra_sql = $sql;
        $count = $this->__commit(true);
        return isset($count[0]['M_COUNTER']) && $count[0]['M_COUNTER'] ? $count[0]['M_COUNTER'] : 0;
    }

    /**
     * 修改Where语句
     * @param array $conditions
     * @return $this
     */
    public function where(array $conditions): SelectOperation
    {
        return parent::where($conditions);
    }

    /**
     * 对某个字段进行求和
     * @param array $conditions 求和条件
     * @param string $param 求和字段
     * @return int|mixed
     */
    public function sum(array $conditions, string $param): mixed
    {
        if (!Field::isName($param)) {
            throw new DbFieldError("字段名称只允许为字母、点、下划线", $param);
        }
        if (!empty($conditions)) $this->where($conditions);

        $sql = /** @lang text */
            "SELECT SUM($param) AS M_COUNTER FROM " . $this->opt['table_name'] . " " . (empty($conditions) ? '' : 'where ' . $this->opt['where']);
        try {
            $this->tra_sql = $sql;
            $count = $this->__commit(true);
        } catch (Exception $e) {
            return 0;
        }
        return isset($count[0]['M_COUNTER']) && $count[0]['M_COUNTER'] ? $count[0]['M_COUNTER'] : 0;
    }

    /**
     * 去重
     * @return $this
     */
    public function distinct()
    {
        $this->opt['distinct'] = "DISTINCT";
        return $this;
    }

    /**
     * 编译
     */
    protected function translateSql(): void
    {
        $sql = $this->getOpt('SELECT', 'distinct');
        $sql .= $this->getOpt('', 'field');
        $sql .= $this->getOpt('FROM', 'table_name');
        $sql .= $this->getOpt('WHERE', 'where');
        $sql .= $this->getOpt('ORDER BY', 'order');
        $sql .= $this->getOpt('GROUP BY', 'group_by');
        $sql .= $this->getOpt('LIMIT', 'limit');
        $this->tra_sql = $sql . ";";
    }


}
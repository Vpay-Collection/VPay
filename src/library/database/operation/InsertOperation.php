<?php
/*
 * Copyright (c) 2023. Ankio. All Rights Reserved.
 */

/**
 * Package: library\database\operation
 * Class InsertOption
 * Created By ankio.
 * Date : 2022/11/16
 * Time : 18:18
 * Description :
 */

namespace library\database\operation;

use cleanphp\base\Error;
use library\database\Db;

class InsertOperation extends BaseOperation
{
    /*数据库常量*/
    const INSERT_NORMAL = 0;
    const INSERT_IGNORE = 1;
    const INSERT_DUPLICATE = 2;

    /**
     * 用来初始化的
     * @param Db $db
     * @param $m
     * @param int $model insert模式
     */
    public function __construct(Db &$db,  $m, int $model = self::INSERT_NORMAL)
    {
        parent::__construct($db, $m);
        $this->opt = [];
        $this->opt['type'] = 'insert';
        $this->opt['model'] = $db->getDriver()->onInsertModel($model);
        $this->bind_param = [];
    }

    /**
     * 设置查询条件
     * @param array $conditions 条件内容，必须是数组,格式如下["name"=>"张三","i > :hello",":hello"=>"hi"]
     */
    public function where(array $conditions): BaseOperation
    {
        return parent::where($conditions);
    }

    /**
     * 设置添加的kv数组
     * @param $kv array 数组对应的插入值
     * @param $udp_keys array 需要更新的字段
     * @return InsertOperation
     */
    public function keyValue(array $kv, array $udp_keys = []): InsertOperation
    {
        $key = array_keys($kv);
        $value = array_values($kv);
        return $this->keys($key, $udp_keys)->values([$value]);
    }

    public function keyValues(array $kv, array $udp_keys = []): InsertOperation{
        $key = array_keys($kv[0]);
        $values = [];
        foreach ($kv as $item){
            $values[] = array_values($item);
        }
        return $this->keys($key, $udp_keys)->values($values);
    }

    /**
     * 插入值
     * @param $row array 需要插入的数组
     */
    public function values(array $row): InsertOperation
    {
        $length = sizeof($row);
        $k = 0;
        $values = [];
        $marks = '';
        for ($i = 0; $i < $length; $i++) {
            $marks .= '(';
            foreach ($row[$i] as $val) {
                $values[":_INSERT_" . $k] = $val;
                $marks .= ":_INSERT_" . $k . ',';
                $k++;

            }
            $marks = rtrim($marks, ",") . '),';
        }
        $marks = rtrim($marks, ",");
        $this->opt['values'] = $marks;
        $this->bind_param += $values;
        return $this;
    }

    /**
     * 需要插入的Key
     * @param array $key
     * @param ?array $columns
     * @return $this
     */
    public function keys(array $key, ?array $columns = []): InsertOperation
    {
        if ($this->opt['model'] == self::INSERT_DUPLICATE && sizeof($columns) == 0) {
            Error::err('DUPLICATE模式必须具有更新字段。', [], "Database Sql");
        }
        $value = '';
        foreach ($key as $v) {
            $value .= "`{$v}`,";
        }
        $value = '(' . rtrim($value, ",") . ')';
        $this->opt['key'] = $value;
        $update = [];
        if (is_array($columns) && sizeof($columns) != 0) {
            foreach ($columns as $k) {
                $update[] = "`{$k}`" . " = VALUES(" . $k . ')';
            }
            $this->opt['columns'] = implode(', ', $update);
        }
        return $this;
    }

    /**
     * 提交修改
     * @return string
     */
    public function commit(): string
    {
        parent::__commit();
        return $this->db->getDriver()->getDbConnect()->lastInsertId();
    }

    /**
     * 构造sql
     */
    protected function translateSql(): void
    {
        $sql = '';
        switch ($this->opt['model']) {
            case self::INSERT_DUPLICATE:
                $sql .= $this->getOpt('INSERT INTO', 'table_name');
                $sql .= $this->getOpt('', 'key');
                $sql .= $this->getOpt('VALUES', 'values');
                $sql .= $this->getOpt('ON DUPLICATE KEY UPDATE', 'columns');
                break;
            case self::INSERT_NORMAL:
                $sql .= $this->getOpt('INSERT INTO', 'table_name');
                $sql .= $this->getOpt('', 'key');
                $sql .= $this->getOpt('VALUES', 'values');
                break;
            case self::INSERT_IGNORE:
                $sql .= $this->getOpt('INSERT IGNORE INTO', 'table_name');
                $sql .= $this->getOpt('', 'key');
                $sql .= $this->getOpt('VALUES', 'values');
                break;
        }
        $this->tra_sql = $sql . ";";

    }

}
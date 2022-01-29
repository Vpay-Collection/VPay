<?php
/*******************************************************************************
 * Copyright (c) 2022. CleanPHP. All Rights Reserved.
 ******************************************************************************/



namespace app\core\database\sql;


use app\core\error\Error;

/**
 * Class Insert
 * @package app\core\database\sql
 * Date: 2020/11/21 12:40 下午
 * Author: ankio
 * Description:插入语句的语法糖包装
 */
class Insert extends sqlBase
{
    /**
     * 用来初始化的
     * @param int $model insert模式
     * @return $this
     */
    public function insert(int $model = SQL_INSERT_NORMAL): Insert
    {
        $this->opt = [];
        $this->opt['tableName'] = $this->tableName;
        $this->opt['type'] = 'insert';
        $this->opt['model'] = $model;
        $this->bindParam = [];
        return $this;
    }

    /**
     * 设置表
     * @param string $tableName
     * @return Insert
     */
    public function table(string $tableName) :Insert
    {
        return parent::table($tableName);
    }

    /**
     * 设置查询条件
     * @param array $conditions 条件内容，必须是数组,格式如下["name"=>"张三","i > :hello",":hello"=>"hi"]
     * @return Insert
     */
    public function where(array $conditions):Insert
    {
        return parent::where($conditions);
    }

    /**
     * 设置添加的kv数组
     * @param $kv array 数组对应的插入值
     * @param $udpKey array|null 需要更新的字段
     * @return Insert
     */
    public function keyValue(array $kv, array $udpKey=null):Insert
    {
        $key = array_keys($kv);
        $value = array_values($kv);
        return $this->keys($key,$udpKey)->values([$value]);
    }

    /**
     * 插入值
     * @param $row array 需要插入的数组
     * @return $this
     */
    public function values(array $row): Insert
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
        $this->bindParam += $values;

        return $this;
    }

    /**
     * 需要插入的Key
     * @param array $key
     * @param ?array $columns
     * @return $this
     */
    public function keys(array $key, ?array $columns = []): Insert
    {
        if ($this->opt['model'] == SQL_INSERT_DUPLICATE && sizeof($columns) == 0) {
            Error::err('数据库错误：DUPLICATE模式必须具有更新字段。');
        }
        $value = '';
        foreach ($key as $v) {
            $value .= "`{$v}`,";
        }
        $value = '(' . rtrim($value, ",") . ')';
        $this->opt['key'] = $value;
        if(is_array($columns)&&sizeof($columns)!=0){
            foreach ($columns as $k) {
                $update[] = "`{$k}`" . " = VALUES(" . $k . ')';
            }
            $this->opt['columns'] = implode(', ', $update);
        }
        return $this;
    }

    /**
     * 提交修改
     * @return mixed
     */
    public function commit()
    {
        $this->translateSql();
        $this->sql->execute($this->traSql, $this->bindParam, false);
        return $this->sql->dbInstance($this->sql->getDbData()[$this->sql->sqlIndex])->lastInsertId();
    }

    /**
     * 构造sql
     */
    private function translateSql()
    {
        $sql = '';
        switch ($this->opt['model']) {
            case SQL_INSERT_DUPLICATE:
                $sql .= $this->getOpt('INSERT INTO', 'tableName');
                $sql .= $this->getOpt('', 'key');
                $sql .= $this->getOpt('VALUES', 'values');
                $sql .= $this->getOpt('ON DUPLICATE KEY UPDATE', 'columns');
                break;
            case SQL_INSERT_NORMAL:
                $sql .= $this->getOpt('INSERT INTO', 'tableName');
                $sql .= $this->getOpt('', 'key');
                $sql .= $this->getOpt('VALUES', 'values');
                break;
            case SQL_INSERT_IGNORE:
                $sql .= $this->getOpt('INSERT IGNORE INTO', 'tableName');
                $sql .= $this->getOpt('', 'key');
                $sql .= $this->getOpt('VALUES', 'values');
                break;
        }
        $this->traSql = $sql . ";";

    }

}

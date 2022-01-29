<?php
/*******************************************************************************
 * Copyright (c) 2022. CleanPHP. All Rights Reserved.
 ******************************************************************************/

namespace app\core\database;
use app\core\database\sql\Delete;
use app\core\database\sql\Insert;
use app\core\database\sql\Page;
use app\core\database\sql\Select;
use app\core\database\sql\sqlExec;
use app\core\database\sql\Update;
use Exception;

/**
 * Class Sql
 * @package app\core\database
 * Date: 2020/11/22 11:05 下午
 * Author: ankio
 * Description:sql的集合类
 */
class Sql
{
    /**
     * @var string
     */
    protected string $sqlIndex = "master";
    /**
     * @var mixed|string
     */
    private $tableName;
    private array $instances = [];
    private ?sqlExec $sql;


    /**
     * Sql constructor.
     * @param string $tableName 数据表名
     */
    public function __construct(string $tableName = '')
    {
        $this->sql = new sqlExec();
        if($tableName!=="")
            $this->tableName = $tableName;
    }

    /**
     * @param string $tableName 表名
     * @return $this
     */
    protected function table(string $tableName): Sql
    {
        $this->tableName = $tableName;
        return $this;
    }

    /**
     * select
     * @param string $field select字段
     * @return Select
     */
    protected function select(string $field = "*"): Select
    {
        return $this->sqlInstance("Select")->select($field);
    }


    /**
     * 获取sql实例
     * @param string $name 获取sql实例
     * @return sqlExec|Insert|Select|Delete|Update
     */
    private function sqlInstance(string $name = "")
    {
        if ($name === "") return $this->sql;//为空直接获取执行实例
        $class = 'app\core\database\sql\\' . $name;
        if (isset($this->instances[$name]) && get_class($this->instances[$name]) === $class)
            return $this->instances[$name];

        if (class_exists($class)) {
            $this->instances[$name] = new $class($this->tableName, $this->sql);
        } else {
            return $this->sql;
        }
        return $this->instances[$name];
    }

    /**
     * 获取分页数据
     * @return Page
     */
    public function getPage(): Page
    {
        return new Page($this->sqlInstance("Select")->getPage());
    }

    /**
     * 删除
     * @return Delete
     */
    protected function delete(): Delete
    {
        return $this->sqlInstance("Delete")->delete();
    }

    /**
     * 插入
     * @param $model int 数据库插入模式
     * @return Insert
     */
    protected function insert(int $model): Insert
    {
        return $this->sqlInstance("Insert")->insert($model);
    }

    /**
     * 更新
     * @return Update
     */
    protected function update(): Update
    {
        return $this->sqlInstance("Update")->update();
    }

    /**
     * 数据库执行
     * @param string $sql 执行的sql语句
     * @param array $params 绑定参数
     * @param false $readonly 是否只读
     * @return array|false|int
       */
    protected function execute(string $sql, array $params = [], bool $readonly = false)
    {
        return $this->sqlInstance()->execute($sql, $params, $readonly);
    }

    /**
     * 输出所有查询的语句
     * @return array
     */
    public function dumpSql(): array
    {
        return $this->sql->dumpSql();
    }

    /**
     * 事务开始
     */
    protected function beginTransaction()
    {
        $this->sqlInstance()->execute("BEGIN");
    }

    /**
     * 事务回滚
     */
    protected function rollBack()
    {
        $this->sqlInstance()->execute("ROLLBACK");
    }

    /**
     * 事务提交
     */
    protected function commit()
    {
        $this->sqlInstance()->execute("COMMIT");
    }

    /**
     * 设置数据库配置文件位置
     * @param $path string 文件位置
     * @param $name string 文件名
     * @return $this
     */
    protected function setDbLocation(string $path, string $name): Sql
    {
        $this->sql->setDbFile($path, $name);
        return $this;
    }

    /**
     * 设置数据库配置文件中的配置选择
     * @param $dbName string 配置文件名
     * @return $this
     */
    protected function setDatabase(string $dbName): Sql
    {
        $this->sql->setDatabase($dbName);
        $this->sqlIndex = $dbName;
        return $this;
    }

    /**
     * 清空数据表
     * @param $table_name string 预清空的数据表
     */
    protected function emptyTable(string $table_name)
    {
        $this->sqlInstance()->emptyTable($table_name);
    }


}

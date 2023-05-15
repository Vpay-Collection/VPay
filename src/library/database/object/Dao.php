<?php
/*
 * Copyright (c) 2023. Ankio. All Rights Reserved.
 */

/**
 * Package: library\database\object
 * Class dao
 * Created By ankio.
 * Date : 2022/11/15
 * Time : 21:15
 * Description :
 */

namespace library\database\object;

use cleanphp\base\Config;
use cleanphp\base\Variables;
use library\database\Db;
use library\database\operation\DeleteOperation;
use library\database\operation\InsertOperation;
use library\database\operation\SelectOperation;
use library\database\operation\UpdateOperation;

abstract class Dao
{

    protected ?Db $db = null;
    protected ?string $model = null;//具体的模型

    /**
     * @param string|null $model 指定具体模型
     */
    public function __construct(string $model = null)
    {
        $this->dbInit();
        $this->model = $model;
    }

    /**
     * 数据库初始化
     * @return void
     */
    protected function dbInit()
    {
        $this->db = Db::init(new DbFile(Config::getConfig("database")["main"]));//数据库初始化
    }

    /**
     * 获取数据库实例
     * @param string|null $model 绑定的具体模型
     * @return $this
     */
    static function getInstance(string $model = null): Dao
    {
        $cls = get_called_class();
        $instance = Variables::get($cls) ?? new static();
        Variables::set($cls, $instance);
        return $instance;
    }

    /**
     * 设置单项
     * @param $key_name
     * @param $key_value
     * @param $set_key
     * @param $set_value
     * @return void
     */
    public function setOption($key_name, $key_value, $set_key, $set_value)
    {
        $this->update()->set([$set_key => $set_value])->where([$key_name => $key_value])->commit();
    }

    /**
     * 更新
     * @return UpdateOperation
     */
    protected function update(): UpdateOperation
    {
        return (new UpdateOperation($this->db, $this, $this->model))->table($this->getTable());
    }

    /**
     * 当前操作的表
     * @return string
     */
    abstract protected function getTable(): string;

    /**
     * 获取指定条件下的数据量
     * @return int|mixed
     */
    function getCount($condition = [])
    {
        return $this->select()->count($condition);
    }

    /**
     * 查找
     * @param ...$field string|Field 需要查询的字段
     * @return SelectOperation
     */
    protected function select(...$field): SelectOperation
    {
        return (new SelectOperation($this->db, $this, $this->model, ...$field))->table($this->getTable());
    }

    /**
     * 获取指定参数的求和
     * @return int|mixed
     */
    function getSum($condition = [], $field = "id")
    {
        return $this->select()->sum($condition, $field);
    }

    /**
     * 删除当前表
     * @return array|int
     */
    public function dropTable()
    {
        return $this->db->execute("DROP TABLE IF EXISTS `{$this->getTable()}`");
    }

    /**
     * 数据库执行
     * @param string $sql 需要执行的sql语句
     * @param array $params 绑定的sql参数
     * @param false $readonly 是否为查询
     * @return array|int
     */
    protected function execute(string $sql, array $params = [], bool $readonly = false)
    {
        return $this->db->execute($sql, $params, $readonly);
    }

    /**
     * 清空当前表
     * @return array|int
     */
    public function emptyTable()
    {
        return $this->db->execute($this->db->getDriver()->renderEmpty($this->getTable()));
    }

    /**
     * 当表被创建的时候
     * @return void
     */
    public function onCreateTable()
    {
    }

    /**
     * 插入模型
     * @param Model $model
     * @return int
     */
    public function insertModel(Model $model): int
    {
        $primary = $this->getAutoPrimary($model);//自增主键不去赋值
        $kv = $model->toArray();
        if ($primary !== null) {
            if (isset($kv[$primary])) unset($kv[$primary]);
        }
        return $this->insert()->keyValue($kv)->commit();
    }

    /**
     * 获取自增主键
     * @param Model $old_model
     * @return string|null
     */
    private function getAutoPrimary(Model $old_model): ?string
    {
        $primary_keys = $old_model->getPrimaryKey() instanceof SqlKey ? [$old_model->getPrimaryKey()] : $old_model->getPrimaryKey();
        /**
         * @var $value SqlKey
         */
        foreach ($primary_keys as $value) {
            if ($value->auto) return $value->name;
        }
        return null;
    }

    /**
     * 插入语句
     * @param int $model
     * @return InsertOperation
     */
    protected function insert(int $model = InsertOperation::INSERT_NORMAL): InsertOperation
    {
        return (new InsertOperation($this->db, $this, $this->model, $model))->table($this->getTable());
    }

    /**
     * 更新模型
     * @param Model $new_model 新的模型
     * @param Model|null $old_model 旧的模型
     * @return bool
     */
    public function updateModel(Model $new_model, Model $old_model = null): bool
    {
        if ($old_model == null) {
            $condition = $this->getPrimaryCondition($new_model);
        } else {
            $condition = $this->getPrimaryCondition($old_model);
        }
        if ($this->find(new Field("id"), $condition) == null) return false;
        //获取到更新数据的条件
        $this->update()->where($condition)->set($new_model->toArray())->commit();
        return true;
    }

    /**
     * 获取主键数组
     * @param Model $old_model
     * @return array
     */
    private function getPrimaryCondition(Model $old_model): array
    {
        $primary_keys = $old_model->getPrimaryKey() instanceof SqlKey ? [$old_model->getPrimaryKey()] : $old_model->getPrimaryKey();
        $condition = [];
        /**
         * @var $value SqlKey
         */
        foreach ($primary_keys as $value) {
            //key
            $name = $value->name;
            //获取主键
            $condition[$name] = $old_model->$name;
        }
        return $condition;
    }

    /**
     * 删除模型
     * @param Model $model
     * @return void
     */
    public function deleteModel(Model $model)
    {
        $condition = $this->getPrimaryCondition($model);
        $this->delete()->where($condition)->commit();
    }

    /**
     * 删除
     * @return DeleteOperation
     */
    protected function delete(): DeleteOperation
    {
        return (new DeleteOperation($this->db, $this, $this->model))->table($this->getTable());
    }

    /**
     * 查找单个数据
     * @param ?Field $field 字段构造
     * @param array $condition 查询条件
     * @return mixed|null
     */
    protected function find(Field $field = null, array $condition = [])
    {
        if ($field === null) $field = new Field();
        $result = $this->select($field)->where($condition)->limit()->commit();
        if (!empty($result)) {
            return $result[0];
        }
        return null;
    }

    /**
     * 事务开始
     */
    protected function affairBegin()
    {
        $this->db->execute("BEGIN");
    }

    /**
     * 事务回滚
     */
    protected function affairRollBack()
    {
        $this->db->execute("ROLLBACK");
    }

    /**
     * 事务提交
     */
    protected function affairCommit()
    {
        $this->db->execute("COMMIT");
    }


    function getAll(?array $fields = [], array $where = [], ?int $start = null, int $size = 10, &$page = null, $object = true)
    {
        if ($fields === null) $fields = [];
        if ($start === null) return $this->select(...$fields)->where($where)->commit();
        return $this->select(...$fields)->page($start, $size, 10, $page)->where($where)->commit($object);
    }


}
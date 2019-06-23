<?php

class temp extends Model
{
    public function __construct($table_name = "tmp_price")
    {
        parent::__construct($table_name);
    }

    public function temp_getAll()
    {//获得所有临时表
        return $this->findAll();
    }

    public function temp_get($id)
    {//查找临时表
        return $this->find(array("oid" => $id));
    }

    public function temp_del($id)
    {//删除临时表
        $this->delete(array("oid" => $id));
    }

    public function temp_insert($condition)
    {//插入临时表
        if (!$this->temp_getByPrice($condition["price"])) {//只有不存在才插入
            $this->insert_ignore($condition);
            return true;
        } else return false;//存在直接返回false
    }

    public function temp_getByPrice($price)
    {//通过价格获得
        return $this->find(array("price" => $price), null, "oid");
    }

}
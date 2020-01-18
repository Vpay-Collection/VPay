<?php
namespace model;
use lib\speed\mvc\Model;
class Temp extends Model
{
    public function __construct($table_name = "pay_tmp_price")
    {
        parent::__construct($table_name);
    }

    public function GetAll()
    {//获得所有临时表
        return $this->selectAll();
    }

    public function GetByOid($id)
    {//查找临时表
        return $this->select(array("oid" => $id));
    }


    public function InsertTemp($condition)
    {//插入临时表
        if(!isset($condition["price"]))return false;
        if (!$this->GetByPrice($condition["price"])) {//只有不存在才插入
            $this->insertIgnore($condition);
            return true;
        } else return false;//存在直接返回false
    }

    public function GetByPrice($price)
    {//通过价格获得
        return $this->select(array("price" => $price), null, "oid");
    }
    //删除过期订单
    public function DelTimeOut(){
        $this->delete(array("timeout <= ".(string)time()));
    }
    public function DelByOid($id){
        $this->delete(array("oid"=>$id));
    }

    public function getByClient($Client){
        return $this->select(array("clientID" => $Client));
    }
    public function deleteByClient($Client){
        return $this->delete(array("clientID" => $Client));
    }
}
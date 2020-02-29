<?php
namespace model;
use lib\speed\mvc\Model;
class Temp extends Model
{
    public function __construct()
    {
        parent::__construct("pay_tmp_price");
    }

    public function getAll()
    {//获得所有临时表
        return $this->selectAll();
    }

    public function getByOid($id)
    {//查找临时表
        return $this->select(array("oid" => $id));
    }


    public function add($condition)
    {//插入临时表
        if(!isset($condition["price"]))return false;
        if (!$this->getByPrice($condition["price"])) {//只有不存在才插入
            $this->insertIgnore($condition);
            return true;
        } else return false;//存在直接返回false
    }

    public function getByPrice($price)
    {//通过价格获得
        return $this->select(array("price" => $price), null, "oid");
    }
    //删除过期订单
    public function delTimeOut(){
        $this->delete(array("timeout <= ".(string)time()));
    }
    public function delByOid($id){
        $this->delete(array("oid"=>$id));
    }

    public function getByClient($Client){
        return $this->select(array("clientID" => $Client));
    }
    public function delByClient($Client){
        return $this->delete(array("clientID" => $Client));
    }
}
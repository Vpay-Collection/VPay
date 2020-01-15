<?php
namespace model;
use lib\speed\mvc\Model;
class PayCode extends Model
{


    public function __construct($table_name = "pay_qrcode")
    {
        parent::__construct($table_name);
    }

//获得二维码列表
    public function GetCodeList($page, $limit, $type = Order::PayAlipay)
    {
        return $this->selectAll(array("type" => $type), "id desc", "*", array($page, $limit));

    }

//删除一个二维码
    public function DeleteCode($id)
    {
        return $this->delete(array("id" => $id));
    }

//添加一张二维码
    public function CreateCode($data, $price, $type = Order::PayAlipay)
    {

        $this->insertIgnore(array("pay_url" => $data, "price" => $price, "type" => $type));

    }

//根据价格与type获得二维码信息
    public function GetCodeOnly($price, $type = Order::PayAlipay)
    {

        return $this->select(array("price" => $price, "type" => $type));

    }
}
<?php

class PayCode extends Model
{
    const WeChat = 1;
    const Alipay = 2;

    public function __construct($table_name = "pay_qrcode")
    {
        parent::__construct($table_name);
    }
//获得二维码列表
    public function GetCode($page, $limit, $type = self::Alipay)
    {
        return $this->findAll(array("type" => $type), "id desc", "*", array($page, $limit));


    }
//删除一个二维码
    public function DeleteCode($id)
    {
        return $this->delete(array("id" => $id));
    }
//添加一张二维码
    public function CreateCode($data, $price, $type = self::Alipay)
    {

        $this->insert_ignore(array("pay_url" => $data, "price" => $price, "type" => $type));

    }
//根据价格与type获得二维码信息
    public function GetCodeOnly($price, $type = self::Alipay)
    {

        return $this->find(array("price" => $price, "type" => $type));

    }
}
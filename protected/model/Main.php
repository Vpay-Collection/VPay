<?php
namespace model;
use lib\speed\mvc\Model;
/*主页统计模块
 * */

class Main extends Model
{
    private $today;

    public function __construct($table_name = "pay_order")
    {
        parent::__construct($table_name);
        $this->today = strtotime(date("Y-m-d"), time());
    }

//取得今天的订单信息
    public function todayOrder()
    {
        $conditions = array(
            "create_date >= :create_date1 and create_date <=:create_date2",
            ":create_date1" => $this->today,
            ":create_date2" => $this->today + 86400
        );
        return $this->selectCount($conditions);
    }

//取得成功的订单信息
    public function todaySuccessOrder()
    {
        $conditions = array(
            "state >= 1 and create_date >= :create_date1 and create_date <=:create_date2",
            ":create_date1" => $this->today,
            ":create_date2" => $this->today + 86400
        );
        return $this->selectCount($conditions);
    }

    public function todayCloseOrder()
    {
        $conditions = array(
            "state = -1 and create_date >= :create_date1 and create_date <=:create_date2",
            ":create_date1" => $this->today,
            ":create_date2" => $this->today + 86400
        );
        return $this->selectCount($conditions);
    }

    public function todayMoney()
    {
        $conditions = array(
            "state >=1 and create_date >= :create_date1 and create_date <=:create_date2",
            ":create_date1" => $this->today,
            ":create_date2" => $this->today + 86400
        );
        return $this->selectSum($conditions, "price");
    }

    //统计支付成功的订单数
    public function countOrder()
    {
        return $this->selectCount(array("state >= 1"));//1是支付完成
    }

    public function countMoney()
    {
        $conditions = array(
            "state >= 1"
        );
        return $this->selectSum($conditions, "price");
    }
}
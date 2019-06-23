<?php

class Main extends Model
{
    private $today;

    public function __construct($table_name = "pay_order")
    {
        parent::__construct($table_name);
        $this->today = strtotime(date("Y-m-d"), time());
    }

    public function todayOrder()
    {
        $conditions = array(
            "create_date >= :create_date1 and create_date <=:create_date2",
            ":create_date1" => $this->today,
            ":create_date2" => $this->today + 86400
        );
        return $this->findCount($conditions);
    }

    public function todaySuccessOrder()
    {
        $conditions = array(
            "state >= 1 and create_date >= :create_date1 and create_date <=:create_date2",
            ":create_date1" => $this->today,
            ":create_date2" => $this->today + 86400
        );
        return $this->findCount($conditions);
    }

    public function todayCloseOrder()
    {
        $conditions = array(
            "state = -1 and create_date >= :create_date1 and create_date <=:create_date2",
            ":create_date1" => $this->today,
            ":create_date2" => $this->today + 86400
        );
        return $this->findCount($conditions);
    }

    public function todayMoney()
    {
        $conditions = array(
            "state >=1 and create_date >= :create_date1 and create_date <=:create_date2",
            ":create_date1" => $this->today,
            ":create_date2" => $this->today + 86400
        );
        return $this->findSum($conditions, "price");
    }

    public function countOrder()
    {
        return $this->findCount(array("state >= 1"));
    }

    public function countMoney()
    {
        $conditions = array(
            "state >= 1"
        );
        return $this->findSum($conditions, "price");
    }
}
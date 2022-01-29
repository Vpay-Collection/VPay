<?php

namespace app\controller\admin;

class Console extends  BaseController
{
    function data(){
        $console=new \app\model\Console();
        $todayOrder = $console->todayOrder();//获得今天订单数量
        $todaySuccessOrder = $console->todaySuccessOrder();//获得今天成功的订单
        $todayCloseOrder = $console->todayCloseOrder();//获取今天关闭的订单
        $todayMoney = $console->todayMoney();//获取今天的收入
        $countOrder = $console->countOrder();//统计总的成功订单数
        $countMoney = $console->countMoney();//统计收到的钱

        return $this->ret(200,null,["todayOrder" => $todayOrder,
            "todaySuccessOrder" => $todaySuccessOrder,
            "todayCloseOrder" => $todayCloseOrder,
            "todayMoney" => round($todayMoney, 2),
            "countOrder" => $countOrder,
            "countMoney" => round($countMoney, 2),]);
    }
}
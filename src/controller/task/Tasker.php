<?php
/*******************************************************************************
 * Copyright (c) 2022. CleanPHP. All Rights Reserved.
 ******************************************************************************/

namespace app\controller\task;

use app\attach\Email;
use app\core\config\Config;
use app\core\debug\Log;
use app\core\web\Response;
use app\lib\Async\Async;
use app\model\Console;

class Tasker extends BaseController
{
    public function init(){
        //响应tasker任务，并对URL进行校验
        Async::getInstance()->response();
    }
    public function taskEveryDay(){
        $pay =  Config::getInstance("pay")->get();

        $mail = new Email();


        $console=new Console();
        $todayOrder = $console->todayOrder();//获得今天订单数量
        $todaySuccessOrder = $console->todaySuccessOrder();//获得今天成功的订单
        $todayCloseOrder = $console->todayCloseOrder();//获取今天关闭的订单
        $todayMoney = $console->todayMoney();//获取今天的收入
        $countOrder = $console->countOrder();//统计总的成功订单数
        $countMoney = $console->countMoney();//统计收到的钱

        $data = ["todayOrder" => $todayOrder,
            "todaySuccessOrder" => $todaySuccessOrder,
            "todayCloseOrder" => $todayCloseOrder,
            "todayMoney" => round($todayMoney, 2),
            "countOrder" => $countOrder,
            "countMoney" => round($countMoney, 2)];


        $tplData = [
            "logo" => Response::getAddress().DS."static".DS."img".DS."face.jpg",
            "sitename" =>$pay["pay"]["siteName"],
            "title" => "收益日报 - ".date("Y年m月d日"),
            "body" => "<p>今日总订单：<span>{$data['todayOrder']}</span></p><p>今日成功：<span>{$data['todaySuccessOrder']}</span></p><p>今日失败：<span>{$data['todayCloseOrder']}</span></p><p>今日总收入：<span>{$data['todayMoney']}</span></p><p>历史订单数：<span>{$data['countOrder']}</span></p><p>历史收入：<span>{$data['countMoney']}</span></p>"
        ];

        $file = $mail->complieNotify("#FF5722", "#fff", $tplData["logo"], $tplData["sitename"], $tplData["title"], $tplData["body"]);
        $mail->send($pay["mail"]["receive"], "{$tplData['sitename']}", $file, $tplData['sitename']);
    }


}
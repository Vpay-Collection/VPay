<?php
/*******************************************************************************
 * Copyright (c) 2022. CleanPHP. All Rights Reserved.
 ******************************************************************************/
/**
 * Class Alipay
 * Created By ankio.
 * Date : 2022/1/31
 * Time : 3:08 下午
 * Description :
 */

namespace app\controller\api;



use app\attach\ConstData;
use app\core\config\Config;
use app\core\debug\Debug;
use app\lib\Alipay\AlipayServiceReceive;
use app\model\Order;


class Alipay extends BaseController
{
    function notify()
    {
        $pay =  Config::getInstance("pay")->get();
        $aliPay = new AlipayServiceReceive($pay["pay"]["alipay_public_key"]);
//验证签名
        $result = $aliPay->rsaCheck($_POST);
       // dump($result);
        //、Debug::i("alipay_notify",print_r(http_build_query($_POST),true));
      //  Debug::i("alipay_notify",print_r($_GET,true));
      //  Debug::i("alipay_notify","检测结果:".print_r($result?"true":"false",true));
        if($result && arg('trade_status') == 'TRADE_SUCCESS'){
            $orderId = arg("out_trade_no");
            $order = new Order();
            $user =  arg('buyer_logon_id');
            $order->setUser($orderId,$user);
            $order->notify($orderId);
            exit('success');
            //处理你的逻辑，例如获取订单号$_POST['out_trade_no']，订单金额$_POST['total_amount']等
            //程序执行完后必须打印输出“success”（不包含引号）。如果商户反馈给支付宝的字符不是success这7个字符，支付宝服务器会不断重发通知，直到超过24小时22分钟。一般情况下，25小时以内完成8次通知（通知的间隔频率一般是：4m,10m,10m,1h,2h,6h,15h）；

        }
        //Debug::i("alipay_notify","签名验证失败？");
        return 'error';
    }
}
<?php
/*
 * Copyright (c) 2023. Ankio.  由CleanPHP4强力驱动。
 */
/**
 * Package: app\controller\api
 * Class Notify
 * Created By ankio.
 * Date : 2023/10/5
 * Time : 14:58
 * Description :
 */

namespace app\controller\api;

use app\database\dao\OrderDao;
use app\utils\alipay\AlipayServiceReceive;
use cleanphp\base\Config;

class Notify extends BaseController
{
    function alipay(){
        $pay =  Config::getConfig("alipay");
        $aliPay = new AlipayServiceReceive($pay["alipay_public_key"]);
        $result = $aliPay->rsaCheck($_POST);
        if($result && arg('trade_status') == 'TRADE_SUCCESS'){
            $orderId = arg("out_trade_no");
            $user =  arg('buyer_logon_id');
            OrderDao::getInstance()->setOption("order_id",$orderId,"user",$user);
            OrderDao::getInstance()->notify($orderId);
            exit('success');
            //处理你的逻辑，例如获取订单号$_POST['out_trade_no']，订单金额$_POST['total_amount']等
            //程序执行完后必须打印输出“success”（不包含引号）。如果商户反馈给支付宝的字符不是success这7个字符，支付宝服务器会不断重发通知，直到超过24小时22分钟。一般情况下，25小时以内完成8次通知（通知的间隔频率一般是：4m,10m,10m,1h,2h,6h,15h）；

        }
        //Debug::i("alipay_notify","签名验证失败？");
        return 'error';
    }
}
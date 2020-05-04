<?php
/**
 * Created by dreamn.
 * Date: 2019-09-28
 * Time: 19:55
 * 在这里修改配置
 */
$key="YtKWARQRpKDCtQs88cC3Finnic5d7iGasGiHwecyZsPM63MHHrp2GCZEpJesYn5kZpWNmGNDMszBwCb4Sb2iGiJPzT6iG8h34szenda7DeMdDfh5yZ3cNRBTFFA8Y6WZ"; //该应用的通讯密钥，如果密钥泄露可以重置密钥
$appid="2";//应用id，在后台应用列表的id部分可以看到
$url="http://a.com"; //支付站点的URL

return array(
    "Appid"=>$appid,//应用id，在后台应用列表的id部分可以看到
    "Key"=>$key,//该应用的通讯密钥，如果密钥泄露可以重置密钥
    "CreateOrder"=>$url."/CreateOrder",//创建订单的接口
    "OrderState"=>$url."/OrderState",//查看订单状态的接口
    "CloseOrder"=>$url."/CloseOrder",//关闭订单的接口
    "Confirm"=>$url."/Confirm",//确认
    "GetOrder"=>$url."/GetOrder",//确认
    'TimeOut'=>'5'
);
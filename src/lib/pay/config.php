<?php
/**
 * Created by dreamn.
 * Date: 2019-09-28
 * Time: 19:55
 * 在这里修改配置
 */
$key="P4HAWM4CRYpsaTTsH85ft78cMnMyRCAZt6h88MBxyCP3SC4KTtR33XN4C7mxMW3z"; //该应用的通讯密钥，如果密钥泄露可以重置密钥
$appid="5";//应用id，在后台应用列表的id部分可以看到
$url= $GLOBALS['http_scheme']  .$_SERVER['HTTP_HOST']; //支付站点的URL

return array(
    "base"=>$url,
    "Appid"=>$appid,//应用id，在后台应用列表的id部分可以看到
    "Key"=>$key,//该应用的通讯密钥，如果密钥泄露可以重置密钥
    "CreateOrder"=>"/CreateOrder",//创建订单的接口
    "OrderState"=>"/OrderState",//查看订单状态的接口
    "CloseOrder"=>"/CloseOrder",//关闭订单的接口
    "Confirm"=>"/Confirm",//确认
    "GetOrder"=>"/GetOrder",//确认
    'TimeOut'=>'5'
);
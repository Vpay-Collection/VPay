<?php
/**
 * Created by dreamn.
 * Date: 2019-09-28
 * Time: 19:55
 * 在这里修改配置
 */
$http_type = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://';

return array(
    "Appid"=>"4",//应用id，在后台应用列表的id部分可以看到
    "Key"=>"fd2kcRrwRhPf7rbniDBc8WNRw8674EwJidssbcS4zfSzCYM2ZGF4GW6n3TiGhma4ytRJ5TWGZnZE3wxMsrQXW2eFHQKeh3yNmZjSwYbZNbx7TE7N8Yzf2iN4Ganti4ZY",//该应用的通讯密钥，如果密钥泄露可以重置密钥
    "CreateOrder"=>$http_type.$_SERVER['HTTP_HOST']."/CreateOrder",//创建订单的接口
    "OrderState"=>$http_type.$_SERVER['HTTP_HOST']."/OrderState",//查看订单状态的接口
    "CloseOrder"=>$http_type.$_SERVER['HTTP_HOST']."/CloseOrder",//关闭订单的接口
    "Confirm"=>$http_type.$_SERVER['HTTP_HOST']."/Confirm",//确认
    "GetOrder"=>$http_type.$_SERVER['HTTP_HOST']."/GetOrder",//确认
    'TimeOut'=>'5',//后台设置的超时时间
);
<?php

date_default_timezone_set('PRC');


$config = array(
    'rewrite' => array(

        'login' => 'admin/main/login',
        'logout' => 'admin/main/logout',
        'AppHeart' => 'App/AppHeart',
        'AppPush' => 'App/AppPush',


        '<m>/<c>/<a>' => '<m>/<c>/<a>',
        '<c>/<a>' => '<c>/<a>',
        'CreateOrder' => 'api/Api/CreateOrder',
        'OrderState' => 'api/Api/OrderState',
        'Confirm' => 'api/Api/Confirm',
        'CloseOrder' => 'api/Api/CloseOrder',
        'GetOrder'=>"api/Api/GetOrder",
        'admin' => 'admin/main/index',
        '/' => 'main/index',
    ),
);


$domain = array(
    $_SERVER["HTTP_HOST"] => array( // 调试配置
        'debug' => 1,
        'mysql' => array(//数据库信息
            'MYSQL_HOST' => 'localhost',
            'MYSQL_PORT' => '3306',
            'MYSQL_USER' => 'vpay_dreamn_cn',
            'MYSQL_DB' => 'vpay_dreamn_cn',
            'MYSQL_PASS' => 'WfLA6PpE8LCZNHnP',
            'MYSQL_CHARSET' => 'utf8',
        ),
        "error" => "error.html"//错误信息
    ),
);
// 为了避免开始使用时会不正确配置域名导致程序错误，加入判断
if (empty($domain[$_SERVER["HTTP_HOST"]])) die("配置域名不正确，请确认" . $_SERVER["HTTP_HOST"] . "的配置是否存在！");

return $domain[$_SERVER["HTTP_HOST"]] + $config;

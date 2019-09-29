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
        'GetOrder' => 'api/Api/GetOrder',
        'CloseOrder' => 'api/Api/CloseOrder',
        'GetState' => 'api/Api/GetState',
        'admin' => 'admin/main/index',
        '/' => 'main/index',
    ),
);


$domain = array(
    "[url]" => array( // 调试配置
        'debug' => 0,//改成0表示不输出详细错误
        'mysql' => array(//数据库信息
            'MYSQL_HOST' => '[host]',
            'MYSQL_PORT' => '[port]',
            'MYSQL_USER' => '[user]',
            'MYSQL_DB' => '[db]',
            'MYSQL_PASS' => '[pass]',
            'MYSQL_CHARSET' => '[charset]',
        ),
        "error" => "error.html"//错误信息
        ),
);
// 为了避免开始使用时会不正确配置域名导致程序错误，加入判断
if (empty($domain[$_SERVER["HTTP_HOST"]])) die("配置域名不正确，请确认" . $_SERVER["HTTP_HOST"] . "的配置是否存在！");

return $domain[$_SERVER["HTTP_HOST"]] + $config;

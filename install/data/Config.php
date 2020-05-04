<?php

namespace app\config;
class Config
{
    static public function register()
    {
        $conf = Config::config();
        if (!in_array($_SERVER["HTTP_HOST"], $conf['host'])) {
            exit('域名绑定错误！');
        }
        $GLOBALS = $conf + Config::route();

    }

    static public function config()
    {
        return array( // 调试配置
            'host' => array('127.0.0.1', '[url]'),//localhost改成自己的域名
            'debug' => 1,//为0不输出调试错误信息
            'mysql' => array(//数据库信息
                'MYSQL_HOST' => '[host]',
                'MYSQL_PORT' => '[port]',
                'MYSQL_USER' => '[user]',
                'MYSQL_DB' => '[db]',
                'MYSQL_PASS' => '[pass]',
                'MYSQL_CHARSET' => '[charset]',
            ),
            "error" => 'error',//非调试状态出错显示的信息
        );
    }

    static public function route()
    {
        return array(
            'rewrite' => array(
                'login'                => 'admin/main/login',
                'logout'               => 'admin/main/logout',
                'AppHeart'             => 'App/AppHeart',
                'AppPush'              => 'App/AppPush',

                'CreateOrder'          => 'api/Api/CreateOrder',
                'OrderState'           => 'api/Api/OrderState',
                'Confirm'              => 'api/Api/Confirm',
                'CloseOrder'           => 'api/Api/CloseOrder',
                'GetOrder'             => "api/Api/GetOrder",
                '<m>/<c>/<a>' => '<m>/<c>/<a>',
                '<c>/<a>' => '<c>/<a>',
                '/' => 'main/index',
                '/admin' => 'admin/main/index',
            ),
        );
    }
}
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
            'host' => array('localhost', 'a.com',NULL),//localhost改成自己的域名
            'debug' => 1,//为0不输出调试错误信息
            'mysql' => array(//数据库信息
                'MYSQL_HOST' => 'localhost',
                'MYSQL_PORT' => '3306',
                'MYSQL_USER' => 'root',
                'MYSQL_DB' => 'test',
                'MYSQL_PASS' => 'root',
                'MYSQL_CHARSET' => 'utf8',
            ),
            "error" => 'error',//非调试状态出错显示的信息
        );
    }

    static public function route()
    {
        return array(
            'rewrite' => array(
                '<m>/<c>/<a>' => '<m>/<c>/<a>',
                '<c>/<a>' => '<c>/<a>',
                '/' => 'main/index',
                '/admin' => 'admin/main/index',
            ),
        );
    }
}
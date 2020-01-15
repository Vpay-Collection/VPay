<?php
namespace Speed;
class Config{
    static public function register(){
        $conf=Config::config();
        if($_SERVER["HTTP_HOST"]!==$conf['host']){
            exit('域名绑定错误！');
        }
        $GLOBALS=$conf+Config::route();

    }
    static public function config(){
        return array( // 调试配置
            'host'=>'pay.dreamn.cn',//localhost改成自己的域名
            'debug' => 1,//为0不输出调试错误信息
            'mysql' => array(//数据库信息
                'MYSQL_HOST' => 'localhost',
                'MYSQL_PORT' => '3306',
                'MYSQL_USER' => 'pay_dreamn_cn',
                'MYSQL_DB'   => 'pay_dreamn_cn',
                'MYSQL_PASS' => 'SAnFXYEWFEf5HKfd',
                'MYSQL_CHARSET' => 'utf8',
            ),
            "error"=>'Error'//非调试状态出错显示的信息
        );
    }
    static public function route(){
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

                '<m>/<c>/<a>'          => '<m>/<c>/<a>',
                '<c>/<a>'              => '<c>/<a>',
                '/'                    => 'main/index',
                'admin'                => 'admin/main/index',
            ),
        );
    }
}
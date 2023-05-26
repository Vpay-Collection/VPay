<?php return array (
    'frame' =>
        array (
            'host' =>
                array (
                    0 => '0.0.0.0',
                ),
            'rewrite' => true,
            'version' => '4.0.0',
            'log' => 30,
            'session' => 'pay_session',
            'view_debug' => false,
        ),

    'route' =>
        array (
            'robots.txt' => 'api_index/main/robots',
            '/' => 'index/main/index',
            'admin' => 'admin/main/index',
            'shop' => 'shop/main/index',
            'pay/<id>' => 'index/main/pay',
            'item/<id>' => 'shop/main/item',
            'order/<a>' => 'api/pay/<a>',
            'image/<type>/<file>' => 'api_index/main/image',
            'api/admin/<c>/<a>' => 'api_admin/<c>/<a>',
            'api/index/<c>/<a>' => 'api_index/<c>/<a>',
            'api/shop/<c>/<a>' => 'api_shop/<c>/<a>',
            'wechat/<c>/<a>' => 'api_wechat/<c>/<a>',
            'api/user/<c>/<a>' => 'api_user/<c>/<a>',
            '<m>/<c>/<a>' => '<m>/<c>/<a>',
        ),
    'mail' =>
        array (
            'received' => '',
            'smtp' => '',
            'send' => '',
            'passwd' => '',
            'port' => 465,
            'pay_success' => false,
            'pay_daily' => false,
        ),

    'database' =>
        array (
            'main' =>
                array (
                    'type' => 'mysql',
                    'host' => 'localhost',
                    'username' => '',
                    'password' => '',
                    'port' => 3306,
                    'db' => '',
                    'charset' => 'utf8mb4',
                ),
        ),
    'login' =>
        array (
            'username' => '',
            'password' => '',
            'image' => '',
        ),
    'shop' =>
        array (
            'state' => true,
            'title' => 'Ankioの小站',
            'notice' => '',
            'host' => '',
            'key' => '',
            'time' => 5,
            'id' => 1,
        ),
    'channel' =>
        array (
            'alipay' => '',
            'wechat' => '',
        ),
    'app' =>
        array (
            'timeout' => 5,
            'key' => '',
            'conflict' => 1,
        ),
);
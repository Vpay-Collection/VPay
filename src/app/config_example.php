<?php return array (
    'frame' =>
        array (
            'host' =>
                array (
                    0 => '0.0.0.0',
                ),
            'version' => '4.0.6',
            'log' => 1,
            'session' => 'pay_session',
        ),
    'route' =>
        array (
            'robots.txt' => 'api_index/main/robots',
            '/' => 'index/main/index',
            'admin' => 'admin/main/index',
            'pay/<id>' => 'index/main/pay',
            'order/<a>' => 'api/pay/<a>',
            'file/<file>' => 'api_index/main/file',
            'api/admin/<c>/<a>' => 'api_admin/<c>/<a>',
            'api/index/<c>/<a>' => 'api_index/<c>/<a>',
            '<m>/<c>/<a>' => '<m>/<c>/<a>',
        ),
    'database' =>
        array (
            'main' =>
                array (
                    'type' => 'mysql',
                    'host' => 'localhost',
                    'username' => 'BT_DB_USERNAME',
                    'password' => 'BT_DB_PASSWORD',
                    'port' => 3306,
                    'db' => 'BT_DB_NAME',
                    'charset' => 'utf8mb4',
                ),
        ),
    'login' =>
        array (
            'username' => 'LOGIN_USERNAME',
            'password' => 'LOGIN_PASSWORD',
            'image' => '/clean_static/img/head.png',
        ),
    'key' => NULL,
    'app' =>
        array (
            'key' => '',
            'timeout' => 5,
            'conflict' => 1,
            'image_alipay' => '',
            'image_wechat' => '',
        ),
);
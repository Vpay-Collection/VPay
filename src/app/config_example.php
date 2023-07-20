<?php return array (
    'frame' =>
        array (
            'host' =>
                array (
                    0 => '0.0.0.0',
                ),
            'rewrite' => true,
            'version' => '4.0.6',
            'log' => 1,
            'session' => 'pay_session'
        ),

    'route' =>
        array (
            'robots.txt' => 'api_index/main/robots',
            '/' => 'index/main/index',
            'admin' => 'admin/main/index',
            'pay/<id>' => 'index/main/pay',
            'item/<id>' => 'shop/main/item',
            'order/<a>' => 'api/pay/<a>',
            'image/<type>/<file>' => 'api_index/main/image',
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
                    'username' => '',
                    'password' => '',
                    'port' => 3306,
                    'db' => '',
                    'charset' => 'utf8mb4',
                ),
        ),

);
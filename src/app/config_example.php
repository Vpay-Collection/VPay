<?php
/*******************************************************************************
 * Copyright (c) 2022. Ankio. All Rights Reserved.
 ******************************************************************************/
/**
 * File config.php
 * Created By ankio.
 * Date : 2023/4/23
 * Time : 19:53
 * Description :
 */


return [
    'frame' => [
        'host' => [
            '0.0.0.0'
        ],
        'rewrite' => true,
        'version' => '1.0.0',
        'log' => 30,
        'session' => 'user_session', //session名称
    ],
    'redis' => [
        'host' => '127.0.0.1',
        'port' => 6379,
    ],
    'route' => [
        '/' => 'index/main/index',
        'login' => 'index/login/index',
        'wechat' => 'index/wechat/set',
        'image/<type>/<file>' => 'index/main/image',
        'permission/<quick>/<type>' => 'index/login/permission',
        '<m>/<c>/<a>' => '<m>/<c>/<a>',
    ],
    'language'=>true,
    'database' => [
        'main' => [
            'type' => 'mysql',
            'host' => 'localhost',
            'username' => 'user_dev_ankio_n',
            'password' => 'WPWGsAMjC7LhESLx',
            'port' => 3306,
            'db' => 'user_dev_ankio_n',
            'charset' => 'utf8mb4',
        ],
    ],
    'websocket' => [
        'ip' => '127.0.0.1',
        'port' => 4405,
    ]
];

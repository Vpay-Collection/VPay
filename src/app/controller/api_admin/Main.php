<?php
/*******************************************************************************
 * Copyright (c) 2022. Ankio. All Rights Reserved.
 ******************************************************************************/

/**
 * Package: app\controller\admin
 * Class Main
 * Created By ankio.
 * Date : 2023/3/13
 * Time : 17:28
 * Description :
 */

namespace app\controller\api_admin;

use app\Application;
use app\utils\ImageUpload;
use app\utils\ImageUploader;
use core\base\Request;
use core\base\Response;
use core\config\Config;
use core\file\Log;
use core\objects\StringBuilder;
use library\user\login\engine\Password;
use library\user\login\Login;

class Main extends BaseController
{
    function menu(): string
    {

        $menuInfo = [
            [
                "name" => "概览",
                "href" => '/admin/main/console',
                "icon" => "bi bi-grid-fill",
            ],
            [
                "name" => "监控渠道配置",
                "icon" => "bi bi-currency-bitcoin",
                'children' => [
                    /* [
                         'name'=>'PC端监控（待完善）',
                         'href'=> '/admin/channel/pc',
                         'icon'=>'bi bi-cloud'
                     ],*/
                    [
                        'name' => 'App监控',
                        'href' => '/admin/channel/app',
                        'icon' => 'bi bi-android2'
                    ],
                    [
                        'name' => '官方渠道',
                        'href' => '/admin/channel/official',
                        'icon' => 'bi bi-alipay'
                    ]
                ],
            ],
            [
                "name" => "系统配置",
                "href" => '/admin/main/system',
                "icon" => "bi bi-gear-wide-connected",
            ],
            [
                "name" => "应用管理",
                "href" => '/admin/app/index',
                "icon" => "bi  bi-app-indicator",
            ],
            [
                "name" => "订单列表",
                "href" => '/admin/order/index',
                "icon" => "bi bi-card-text",
            ],
            [
                "name" => "内置商城",
                "icon" => "bi bi-shop",
                'children' => [
                    [
                        'name' => '系统配置',
                        'href' => '/admin/shop/setting',
                        'icon' => 'bi bi-gear'
                    ],
                    [
                        'name' => '商品管理',
                        'href' => '/admin/shop/list',
                        'icon' => 'bi bi-list-columns'
                    ],

                ],

            ],
            [
                "name" => "开发文档",
                "href" => '/admin/main/docs',
                "icon" => "bi bi-book-fill",
            ],
        ];


        $ret = [
            "homeInfo" => [
                "title" => $menuInfo[0]["name"],
                "href" => $menuInfo[0]["href"]
            ],
            "menuInfo" => $menuInfo
        ];

        return Application::json(200, null, $ret);
    }

    function heart(): string
    {
        return Application::json(200);
    }

    function system()
    {
        $login = Config::getConfig("login");
        $pay = Config::getConfig("pay");
        $mail = Config::getConfig("mail");
        //系统配置
        if (Request::isGet()) return $this->json(200, null, array_merge($login, $pay, $mail));
        if (!(new Password())->change()) {
            return $this->json(401, "密码修改失败");
        }
        foreach ($pay as $item => &$value) {
            $post = post($item, '');
            if ((new StringBuilder($item))->endsWith("_qr")) {
                $image = new ImageUpload("pay");
                $image->delOneImage($value);
                $post = $image->useOneImage($post);
            }
            $value = $post;
        }
        Config::setConfig("pay", $pay);
        foreach ($mail as $item => &$value) $value = post($item, '');
        Config::setConfig("mail", $mail);
        return $this->json(200);

    }

    function upload(): string
    {
        if (Request::isPost()) {
            $filename = "";
            if ((new ImageUpload())->upload($filename)) {
                return $filename;
            }
            return $this->json(500, $filename);
        } elseif (Request::getRequestMethod() === "GET") {
            if (($load = arg("load")) !== null) {
                $load = urldecode($load);
                if (!(new StringBuilder($load))->startsWith("http")) {
                    $load = url("index", "main", "image", ["type" => "temp", "file" => $load]);
                }
                Response::location(urldecode($load));
            }
        }
        return $this->json(200);
    }
}
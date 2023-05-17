<?php
/*******************************************************************************
 * Copyright (c) 2022. Ankio. All Rights Reserved.
 ******************************************************************************/

/**
 * Package: app\controller\admin
 * Class BaseController
 * Created By ankio.
 * Date : 2023/3/14
 * Time : 11:06
 * Description :
 */

namespace app\controller\admin;


use app\utils\GithubUpdater;
use cleanphp\base\Config;
use cleanphp\base\Controller;
use cleanphp\base\Cookie;
use cleanphp\base\Request;
use cleanphp\base\Response;
use cleanphp\engine\EngineManager;
use library\login\LoginManager;
use library\task\TaskerManager;
use library\task\TaskerTime;

class BaseController extends Controller
{
    public function __init()
    {
        if (!LoginManager::init()->isLogin()) {
            Response::location(LoginManager::init()->getLoginUrl());
        }
        $user = LoginManager::init()->getUser();
        if(!TaskerManager::has("Github更新检测")){
            TaskerManager::add(TaskerTime::day(12,00),GithubUpdater::init("Vpay-Collection/Vpay"),"Github更新检测",-1,true);
        }
        $result = GithubUpdater::init("Vpay-Collection/Vpay")->check(Config::getConfig("frame")["version"], $new, $download, $body);
        EngineManager::getEngine()->setLayout("layout")
            ->setData("theme", Cookie::getInstance()->get("theme", "light"))
            ->setData("color", Cookie::getInstance()->get("theme") === "dark" ? "dark" : "white")
            ->setData("pjax", Request::isPjax())
            ->setData("host", Request::getNowAddress())
            ->setData("username", $user["username"])
            ->setData("image", $user["image"])
            ->setData("update", $result)
            ->setData("new_version", $new)
            ->setData("download", $download)
            ->setData("body", $body)
            ->setData("nav", [
                [
                    'name' => "概览",
                    'href' => url('admin', 'main', 'index'),
                    'pjax' => true,
                    'icon' => 'fas fa-box'
                ],
                [
                    "name" => "App监控",
                    "href" => url('admin', 'channel', 'index'),
                    'pjax' => true,
                    'icon' => 'fab fa-apple'
                ],
                [
                    "name" => "通知配置",
                    "href" => url('admin', 'notice', 'index'),
                    'pjax' => true,
                    'icon' => 'far fa-envelope'
                ],//
                [
                    "name" => "应用管理",
                    "href" => url('admin', 'app', 'index'),
                    'pjax' => true,
                    'icon' => 'fab fa-app-store-ios'
                ],

                [
                    "name" => "订单列表",
                    "href" => url('admin', 'order', 'index'),
                    'pjax' => true,
                    'icon' => 'fas fa-table-list'
                ],
                [
                    "name" => "内置商城",
                    'pjax' => true,
                    'icon' => 'fas fa-store',
                    'children' => [
                        [
                            'name' => '系统配置',
                            'href' => url('admin', 'shop', 'setting'),
                            'icon' => 'fas fa-gear'

                        ],
                        [
                            'name' => '商品分类',
                            'href' => url('admin', 'shop', 'category'),
                            'icon' => 'fas fa-fax'

                        ],
                        [
                            'name' => '商品管理',
                            'href' => url('admin', 'shop', 'manager'),
                            'icon' => 'fas fa-cart-shopping'

                        ]
                    ],
                ],
                [
                    'name' => "个人中心",
                    'href' => url('admin', 'user', 'index'),
                    'pjax' => true,
                    'icon' => 'far fa-user'

                ],
                [
                    "name" => "开发文档",
                    "href" => "https://pay-doc.ankio.net",
                    "icon" => "fas fa-book",
                ]
            ]);
    }
}
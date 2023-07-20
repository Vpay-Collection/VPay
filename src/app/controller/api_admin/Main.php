<?php
/*
 * Copyright (c) 2023. Ankio.  由CleanPHP4强力驱动。
 */
/**
 * Package: app\controller\api_admin
 * Class Main
 * Created By ankio.
 * Date : 2023/7/16
 * Time : 07:34
 * Description :
 */

namespace app\controller\api_admin;

use library\login\LoginManager;

class Main extends BaseController
{
    function menu(){
        $user = LoginManager::init()->getUser();

        return $this->render(200,null,[
            'user' => [
                "image" => $user["image"],
                "name" => $user["username"]
            ],
           'menu'=>[
               [
                   'name' => "概览",
                   'href' => url('admin', 'main', 'index'),
                   'inner' => true,
                   'icon' => 'fas fa-box'
               ],
               [
                   "name" => "App监控",
                   "href" => url('admin', 'channel', 'index'),
                   'inner' => true,
                   'icon' => 'fab fa-apple'
               ],
               [
                   "name" => "通知配置",
                   "href" => url('admin', 'notice', 'index'),
                   'inner' => true,
                   'icon' => 'fas fa-envelope'
               ],//
               [
                   "name" => "应用管理",
                   "href" => url('admin', 'app', 'index'),
                   'inner' => true,
                   'icon' => 'fab fa-app-store-ios'
               ],

               [
                   "name" => "订单列表",
                   "href" => url('admin', 'order', 'index'),
                   'inner' => true,
                   'icon' => 'fas fa-table-list'
               ],
               [
                   'name' => "个人中心",
                   'href' => url('admin', 'user', 'index'),
                   'inner' => true,
                   'icon' => 'fas fa-user'

               ],
               [
                   "name" => "开发文档",
                   "href" => "https://pay-doc.ankio.net",
                   "icon" => "fas fa-book",
               ]
           ]
        ]);
    }
}
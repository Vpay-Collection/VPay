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

use app\database\dao\OrderDao;
use cleanphp\base\Response;
use cleanphp\engine\EngineManager;
use library\login\LoginManager;

class Main extends BaseController
{
    function menu(): string
    {
        $user = LoginManager::init()->getUser();

        return $this->render(200,null,[
            'home'=>[
                'name' => "概览",
                'href' => "admin/console/index",
            ],
            'user' => [
                "image" => $user["image"],
                "name" => $user["username"]
            ],
           'menu'=>[
               [
                   'name' => "概览",
                   'href' => 'admin/console/index',
                 
                   'icon' => 'fas fa-box'
               ],
               [
                   "name" => "支付宝配置",
                   "href" => 'admin/channel/index',
                 
                   'icon' => 'fab fa-alipay'
               ],
               [
                   "name" => "通知配置",
                   "href" => 'admin/notice/index',
                 
                   'icon' => 'fas fa-envelope'
               ],//
               [
                   "name" => "应用管理",
                   "href" => 'admin/app/index',
                 
                   'icon' => 'fab fa-app-store-ios'
               ],

               [
                   "name" => "订单列表",
                   "href" => 'admin/order/index',
                 
                   'icon' => 'fas fa-table-list'
               ],
               [
                   'name' => "个人中心",
                   'href' => 'admin/user/index',
                 
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

    function console(){
        [$day, $data] = OrderDao::getInstance()->countData();
        return $this->render(200,null,[
            "total_price"=> OrderDao::getInstance()->getTotal(),
            "today_price"=>OrderDao::getInstance()->getToday(),
            "payments"=> OrderDao::getInstance()->getRecently(10),
            "day"=>array_reverse($day),
            "data"=>array_reverse($data)
        ]);
    }

    function logout(){
        LoginManager::init()->logout();
        Response::location("/");
    }
}
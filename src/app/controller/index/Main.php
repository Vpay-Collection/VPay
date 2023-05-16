<?php
/*******************************************************************************
 * Copyright (c) 2022. Ankio. All Rights Reserved.
 ******************************************************************************/

/**
 * Package: app\controller\index
 * Class Main
 * Created By ankio.
 * Date : 2023/3/13
 * Time : 14:59
 * Description :
 */

namespace app\controller\index;


use app\database\dao\AppDao;
use app\database\dao\OrderDao;
use cleanphp\base\Config;
use cleanphp\base\Controller;
use cleanphp\base\Request;
use cleanphp\base\Response;
use cleanphp\base\Route;
use cleanphp\base\Session;
use cleanphp\base\Variables;
use cleanphp\engine\EngineManager;
use library\login\LoginManager;


class Main extends Controller
{
    function index()
    {
        if (Config::getConfig("shop")['state']) {
            Response::location(url("shop", "main", "index"));
        }
        (new Response())->render(EngineManager::getEngine()->renderMsg(false, 403, sprintf("Forbidden"), sprintf("You have no access!"), -1, "https://github.com/Vpay-Collection/VPay", "Github"))->send();
    }

    function login()
    {

        if (LoginManager::init()->isLogin()) {
            $default = url("admin", "main", "index");
            $redirect = arg("redirect", $default);
            $parse_url = parse_url($redirect);
            if (!isset($parse_url['host']) && !isset($parse_url['scheme']) && $parse_url['host'] !== Request::getDomainNoPort()) {
                $redirect = $default;
            }
            Response::location($redirect);
        }
        EngineManager::getEngine()->setLayout("layout")->setData("title", "Vpay管理后台");
        if (!empty(Config::getConfig("sso"))) {
            Response::location(LoginManager::init()->getLoginUrl());
        }
    }

    function robots(): string
    {
        return <<<EOF
User-agent: *

Disallow: /*
EOF;
    }

    function image()
    {
        $file = get('file', '');
        Route::renderStatic(Variables::getStoragePath("uploads", get("type", "temp"), $file));
    }

    function fast()
    {
        $key = arg("key");
        $hash = md5(Response::getHttpScheme() . Request::getDomain() . Config::getConfig("app")['key']);

        if ($key === $hash) {
            LoginManager::init()->setLogin();
            Response::location(url('admin', 'main', 'index'));
        }
        Response::location("/");
    }

    function pay()
    {

        $id = arg("id");

        $order = OrderDao::getInstance()->getByOrderIdWait($id);

        if (empty($order)) {
            (new Response())->render(EngineManager::getEngine()->renderMsg(false, 400, "无订单", "该订单已支付或已关闭", -1, "/", "返回"))->send();
        }
        $app = AppDao::getInstance()->getByAppId($order->appid);
        if (empty($app)) {
            (new Response())->render(EngineManager::getEngine()->renderMsg(false, 400, "无订单", "该订单已支付或已关闭", -1, "/", "返回"))->send();
        }
        Session::getInstance()->set("order_id", $id);
        EngineManager::getEngine()->setLayout("layout")
            ->setData("app", $app->toArray())
            ->setData("title", $app->app_name . "收银台")
            ->setData("mail", Config::getConfig("mail")["received"])
            ->setData("timeout", Config::getConfig("app")["timeout"])
            ->setArray($order->toArray());

    }
}
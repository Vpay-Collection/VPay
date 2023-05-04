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


use cleanphp\base\Config;
use cleanphp\base\Controller;
use cleanphp\base\Response;
use cleanphp\base\Route;
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
            Response::location(url('admin', 'main', 'index'));
        }
        EngineManager::getEngine()->setLayout("layout");
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
}
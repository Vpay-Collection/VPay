<?php
/*
 * Copyright (c) 2023. Ankio.  由CleanPHP4强力驱动。
 */

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
        $result = GithubUpdater::init("Vpay-Collection/Vpay")->check(Config::getConfig("frame")["version"], $new, $download, $body);
        EngineManager::getEngine()->setLayout("layout")
            ->setData("pjax", Request::isPjax())
            ->setData("host", Request::getNowAddress())
            ->setData("update", $result)
            ->setData("new_version", $new)
            ->setData("download", $download)
            ->setData("body", $body);
        if(Request::isPjax()){
            //不是pjax请求加载layout
            EngineManager::getEngine()->setLayout(null);
        }
    }
}
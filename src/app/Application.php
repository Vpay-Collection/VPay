<?php
/*******************************************************************************
 * Copyright (c) 2022. Ankio. All Rights Reserved.
 ******************************************************************************/

/**
 * Package: app\main
 * Class Application
 * Created By ankio.
 * Date : 2023/4/24
 * Time : 12:39
 * Description :
 */

namespace app;

use app\task\DaemonTasker;
use cleanphp\base\Config;
use cleanphp\base\Cookie;
use cleanphp\base\EventManager;
use cleanphp\base\MainApp;
use cleanphp\base\Response;
use cleanphp\base\Session;
use cleanphp\base\Variables;
use cleanphp\cache\Cache;
use cleanphp\engine\EngineManager;
use cleanphp\engine\JsonEngine;
use cleanphp\engine\ViewEngine;
use cleanphp\objects\StringBuilder;
use library\task\TaskerManager;
use library\task\TaskerTime;

class Application implements MainApp
{


    /**
     * @inheritDoc
     */
    function onRequestArrive()
    {
        include_once Variables::getLibPath("vpay", "src", "autoload.php");
        //作为资源服务器，会话有效期至少持续60天
        Session::getInstance()->start(3600 * 24 * 60);
        $string = new StringBuilder(Variables::get("__request_module__"));
        if ($string->startsWith("api")) {
            EngineManager::setDefaultEngine(new JsonEngine(["code" => 0, "msg" => "OK", "data" => null, "count" => 0]));
        } else {
            EngineManager::setDefaultEngine(new ViewEngine());
            EngineManager::getEngine()->setData("__version", Config::getConfig('frame')['version']);
            //刷新一下客户端主题，方便渲染
            if (Cookie::getInstance()->get("theme") == null) {
                (new Response())->render(<<<EOF
 <script>
    function isDarkMode() {
      return window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
    }
    document.cookie = "theme=" + (isDarkMode()?"dark":"light") + "; path=/; max-age=" +   2 * 60 * 60; 
    location.reload()
  </script>
EOF
                )->send();
            }
            EngineManager::getEngine()->setData("theme", Cookie::getInstance()->get("theme"));
            //跳转安装

            if (empty(Cache::init()->get("install")) && Variables::get("__request_controller__") !== "install") {
                Response::location(url("index", 'install', 'index'));
            }

        }


        if (!TaskerManager::has("App心跳守护进程")) {
            TaskerManager::add(TaskerTime::nHour(1, 0), new DaemonTasker(), "App心跳守护进程", -1);
        }


    }


    /**
     * @inheritDoc
     */
    function onRequestEnd()
    {

    }

    function onFrameworkStart()
    {
        //渲染json
        EventManager::addListener('__json_render_msg__', function (string $event, &$data) {
            $data = ["code" => $data['code'], "msg" => $data['msg'], "data" => $data['data']];
        });
        //渲染view
        EventManager::addListener('__view_render_msg__', function (string $event, &$data) {
            $render_data = $data['data'];
            if ($data['data']['code'] === 404 || $data['data']['code'] === 403 || $data['data']['code'] === 500)
                $render_data['error_code'] = $data['data']['code'];
            else
                $render_data['error_code'] = 500;


            $data['tpl'] = EngineManager::getEngine()
                ->setLayout(null)
                ->setData("theme", Cookie::getInstance()->get("theme", 'light'))
                ->setTplDir(Variables::getViewPath('error'))
                ->setEncode(false)
                ->setArray($render_data)
                ->render('error');


        });

    }
}
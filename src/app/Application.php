<?php
/*
 * Copyright (c) 2023. Ankio.  由CleanPHP4强力驱动。
 */

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
use app\utils\GithubUpdater;
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
use library\task\TaskerManager;
use library\task\TaskerTime;

class Application implements MainApp
{


    /**
     * @inheritDoc
     */
    function onRequestArrive(): void
    {
        Session::getInstance()->start();//会话有效即可
        if (str_starts_with(Variables::get("__request_module__"),"api")) {
            EngineManager::setDefaultEngine(new JsonEngine(["code" => 0, "msg" => "OK", "data" => null, "count" => 0]));
        } else {
            EngineManager::setDefaultEngine(new ViewEngine());
            EngineManager::getEngine()->setData("__version", Config::getConfig('frame')['version'])->setData("__lang", Variables::get("__lang", "zh-cn"));
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

            if (empty(Cache::init(0,Variables::getCachePath('cleanphp',DS))->get("install.lock")) && Variables::get("__request_controller__") !== "install") {
                Response::location(url("index", 'install', 'index'));
            }

        }


        if (!TaskerManager::has("App心跳守护进程")) {
            TaskerManager::add(TaskerTime::nHour(1, 0), new DaemonTasker(), "App心跳守护进程", -1);
        }
        if(!TaskerManager::has("Github更新检测")){
            TaskerManager::add(TaskerTime::day(12,00),GithubUpdater::init("Vpay-Collection/Vpay"),"Github更新检测",-1);
        }

    }

    function onFrameworkStart(): void
    {
        //渲染json
        EventManager::addListener('__json_render_msg__', function (string $event, &$data) {
            $data = ["code" => $data['code'], "msg" => $data['msg'], "data" => $data['data']];
        });
        //渲染view
        EventManager::addListener('__view_render_msg__', function (string $event, &$data) {
            $render_data = $data['data'];

            $data['tpl'] = EngineManager::getEngine()
                ->setLayout(null)
                ->setData("__version", Config::getConfig('frame')['version'])
                ->setData("__lang", Variables::get("__lang", "zh-cn"))
                ->setData("theme", Cookie::getInstance()->get("theme", 'light'))
                ->setTplDir(Variables::getViewPath('error'))
                ->setEncode(false)
                ->setArray($render_data)
                ->render('error');


        });
        EventManager::addListener("__on_view_render__",function (string $event, &$data){
            $theme =  Cookie::getInstance()->get("theme",'light');
            if($theme==="dark"){
                $data = str_replace("-light","-dark",$data);
            }else{
                $data = str_replace("-dark","-light",$data);
            }
        });
    }

    function onRequestEnd(): void
    {

    }
}
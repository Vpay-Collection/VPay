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


use app\utils\GithubUpdater;
use cleanphp\App;
use cleanphp\base\Config;
use cleanphp\base\Cookie;
use cleanphp\base\EventManager;
use cleanphp\base\MainApp;
use cleanphp\base\Response;
use cleanphp\base\Route;
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

        EngineManager::setDefaultEngine(new JsonEngine(["code" => 0, "msg" => "OK", "data" => null, "count" => 0]));


    }

    function onFrameworkStart(): void
    {
        if (empty(Cache::init(0,Variables::getCachePath('cleanphp',DS))->get("install.lock")) && Variables::get("__request_controller__") !== "install") {
            App::exit(EngineManager::getEngine()->render(302,"install","/@install"),true);
        }
        //会话有效期持续7天
        Session::getInstance()->start();

        $uriWithoutQuery = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        if(in_array($uriWithoutQuery,['','/'])){
            Response::location("/@");
        }
        //渲染json
        EventManager::addListener('__json_render_msg__', function (string $event, &$data) {
            $data = ["code" => $data['code'], "msg" => $data['msg'], "data" => $data['data']];
        });

    }

    function onRequestEnd(): void
    {

    }
}
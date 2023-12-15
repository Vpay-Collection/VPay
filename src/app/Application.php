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


use app\controller\admin\App;
use app\database\dao\AppDao;
use app\database\dao\ShopDao;
use app\database\model\FileModel;
use cleanphp\base\Config;
use cleanphp\base\EventManager;
use cleanphp\base\MainApp;
use cleanphp\base\Response;
use cleanphp\base\Session;
use cleanphp\base\Variables;
use cleanphp\cache\Cache;
use cleanphp\engine\EngineManager;
use cleanphp\engine\JsonEngine;

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
        $uriWithoutQuery = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        if (empty(Cache::init(0,Variables::getCachePath('cleanphp',DS))->get("install.lock")) && !str_contains($uriWithoutQuery,"/install/")) {
           Response::location("/@install");
        }
        //会话有效期持续7天
        Session::getInstance()->start();

        if(in_array($uriWithoutQuery,['','/'])){
            $shop = Config::getConfig("shop");
            if($shop && $shop['state']===1){
                Response::location("/@shop");
            }else{
                Response::location("/@");
            }

        }
        //渲染json
        EventManager::addListener('__json_render_msg__', function (string $event, &$data) {
            $data = ["code" => $data['code'], "msg" => $data['msg'], "data" => $data['data']];
        });

        EventManager::addListener('__deleteTimeoutFile__',function(string $event, &$data){
            /**
             * @var $data FileModel
             */
            //首先检查logo
            if(
                str_contains(Config::getConfig("login")["image"],$data->name) ||
                AppDao::getInstance()->findImage($data->name) ||
                ShopDao::getInstance()->findImage($data->name)
            ) {
                $data->count = 1;
            }

        });
    }

    function onRequestEnd(): void
    {

    }
}
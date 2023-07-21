<?php
/*
 * Copyright (c) 2023. Ankio.  由CleanPHP4强力驱动。
 */

/**
 * Package: app\controller\admin
 * Class Channel
 * Created By ankio.
 * Date : 2023/5/4
 * Time : 13:05
 * Description :
 */

namespace app\controller\admin;

use app\objects\config\ChannelConfig;
use cleanphp\base\Config;
use cleanphp\base\Request;
use cleanphp\base\Response;
use cleanphp\base\Variables;
use cleanphp\cache\Cache;
use cleanphp\engine\EngineManager;

class Channel extends BaseController
{
    function index()
    {

        $last = Cache::init()->get("last_heart");
        if($last===null){
            EngineManager::getEngine()->setData("status",0);
        }elseif (time() - $last <= 60 * 15){
            EngineManager::getEngine()->setData("status",1);
            EngineManager::getEngine()->setData("last_heart", date("Y-m-d H:i:s", $last));
        }else{
            EngineManager::getEngine()->setData("status",2);
        }

        $app = new ChannelConfig(Config::getConfig("app"),false);
        if (empty($app->key)) {
            $app->key = rand_str(32);
            Config::setConfig('app', $app->toArray());
        }
        EngineManager::getEngine()->
        setData("qrcode", url("api", "image", "qrcode", [
            'url' => json_encode([
                'url' => Response::getHttpScheme() . Request::getDomain(),
                'key' => $app->key
            ])
        ]));
    }
}
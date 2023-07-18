<?php
/*******************************************************************************
 * Copyright (c) 2022. Ankio. All Rights Reserved.
 ******************************************************************************/

/**
 * Package: app\controller\admin
 * Class Channel
 * Created By ankio.
 * Date : 2023/5/4
 * Time : 13:05
 * Description :
 */

namespace app\controller\admin;

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

        $app = Config::getConfig("app");
        $key = $app['key'];
        if (empty($key)) {
            $key = rand_str(32);
            $app['key'] = $key;
            Config::setConfig('app', $app);
        }
        EngineManager::getEngine()->
        setData("qrcode", url("api", "image", "qrcode", [
            'url' => json_encode([
                'url' => Response::getHttpScheme() . Request::getDomain(),
                'key' => $key
            ])
        ]));
    }
}
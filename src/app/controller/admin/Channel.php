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
use cleanphp\base\Variables;
use cleanphp\cache\Cache;
use cleanphp\engine\EngineManager;

class Channel extends BaseController
{
    function index()
    {
        EngineManager::getEngine()->setArray(Config::getConfig("channel"));
        $last = Cache::init()->get("last_heart");
        $online = false;
        if (time() - $last <= 120) {
            $online = true;
        }
        EngineManager::getEngine()->setData("last_heart", date("Y-m-d H:i:s", $last));
        EngineManager::getEngine()->setData("online", $online);
        $app = Config::getConfig("app");
        $key = $app['key'];
        if (empty($key)) {
            $key = rand_str(32);
            $app['key'] = $key;
            Config::setConfig('app', $app);
        }
        EngineManager::getEngine()->
        setArray($app)->
        setData("qrcode", url("api", "image", "qrcode", [
            'url' => json_encode([
                'url' => Variables::get("__http_scheme__") . Request::getDomain(),
                'key' => $key
            ])
        ]));
    }
}
<?php
/*******************************************************************************
 * Copyright (c) 2022. Ankio. All Rights Reserved.
 ******************************************************************************/

/**
 * Package: app\controller\admin
 * Class Notice
 * Created By ankio.
 * Date : 2023/5/4
 * Time : 16:27
 * Description :
 */

namespace app\controller\admin;

use cleanphp\base\Config;
use cleanphp\engine\EngineManager;

class Notice extends BaseController
{
    function index()
    {
        EngineManager::getEngine()->setData("sso", Config::getConfig("sso") !== null);
        EngineManager::getEngine()->setArray(Config::getConfig("mail"));
    }
}
<?php
/*******************************************************************************
 * Copyright (c) 2022. Ankio. All Rights Reserved.
 ******************************************************************************/

/**
 * Package: app\controller\admin
 * Class User
 * Created By ankio.
 * Date : 2023/5/4
 * Time : 18:01
 * Description :
 */

namespace app\controller\admin;

use cleanphp\base\Config;
use cleanphp\engine\EngineManager;

class User extends BaseController
{
    function index()
    {
        EngineManager::getEngine()->setArray(Config::getConfig("login"));
    }
}
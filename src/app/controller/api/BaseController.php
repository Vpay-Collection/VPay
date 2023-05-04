<?php
/*******************************************************************************
 * Copyright (c) 2022. Ankio. All Rights Reserved.
 ******************************************************************************/

/**
 * Package: app\controller\api
 * Class BaseController
 * Created By ankio.
 * Date : 2023/3/13
 * Time : 17:25
 * Description :
 */

namespace app\controller\api;

use app\Application;
use cleanphp\base\Controller;
use cleanphp\engine\EngineManager;

class BaseController extends Controller
{

    const API_ERROR = 401;
    const API_SUCCESS = 200;

    public function json($code = 200, $msg = "OK", $data = null, $count = 0): string
    {
        return EngineManager::getEngine()->render($code, $msg, $data, $count);
    }
}
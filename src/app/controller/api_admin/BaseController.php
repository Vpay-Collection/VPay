<?php
/*
 * Copyright (c) 2023. Ankio.  由CleanPHP4强力驱动。
 */

/**
 * Package: app\controller\admin
 * Class BaseController
 * Created By ankio.
 * Date : 2023/3/13
 * Time : 17:28
 * Description :
 */

namespace app\controller\api_admin;

use cleanphp\base\Controller;
use cleanphp\base\Variables;
use cleanphp\engine\EngineManager;
use library\login\LoginManager;

class BaseController extends Controller
{
    public function __init(): ?string
    {

        if (!LoginManager::init()->isLogin()) {
            return $this->render(401, LoginManager::init()->getLoginUrl());
        }
        return null;
    }

    public function json($code = 200, $msg = "OK", $data = null, $count = 0): string
    {
        return EngineManager::getEngine()->render($code, $msg, $data, $count);
    }


}
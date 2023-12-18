<?php
/*
 * Copyright (c) 2023. Ankio.  由CleanPHP4强力驱动。
 */

/**
 * Package: app\controller\api_admin
 * Class Main
 * Created By ankio.
 * Date : 2023/5/6
 * Time : 21:33
 * Description :
 */

namespace app\controller\admin;

use library\login\LoginManager;

class User extends BaseController
{
    function info(): string
    {
        return   $this->render(200,null,LoginManager::init()->getUser());
    }
}
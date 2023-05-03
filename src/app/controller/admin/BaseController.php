<?php
/*******************************************************************************
 * Copyright (c) 2022. Ankio. All Rights Reserved.
 ******************************************************************************/

/**
 * Package: app\controller\admin
 * Class BaseController
 * Created By ankio.
 * Date : 2023/3/14
 * Time : 11:06
 * Description :
 */

namespace app\controller\admin;

use core\base\Response;
use library\user\login\Login;

class BaseController extends \core\base\Controller
{
    public function __init()
    {
        if (!Login::isLogin()) {
            Response::location("/login");
        }
    }
}
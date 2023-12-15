<?php
/*
 * Copyright (c) 2023. Ankio.  由CleanPHP4强力驱动。
 */

/**
 * Package: app\controller\api
 * Class Image
 * Created By ankio.
 * Date : 2023/3/20
 * Time : 12:39
 * Description :
 */

namespace app\controller\api;

use cleanphp\base\Config;
use cleanphp\base\Variables;
use library\qrcode\Code;

class Image extends BaseController
{
    function qrcode(): void
    {
        Code::encode(urldecode(arg('url', "")),Config::getConfig("login")['image']);
    }

}
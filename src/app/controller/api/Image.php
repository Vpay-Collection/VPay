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
use cleanphp\base\Request;
use cleanphp\base\Variables;
use library\qrcode\Code;

class Image extends BaseController
{
    function qrcode(): void
    {
        $url = Config::getConfig("login")['image'];

        if(!str_starts_with($url,"http")){
            $url = Request::getAddress()."/@static/".$url;
        }

        Code::encode(urldecode(arg('url', "")),$url);
    }

}
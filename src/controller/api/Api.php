<?php

namespace app\controller\api;

use app\attach\Image;
use app\core\config\Config;
use app\core\utils\StringUtil;
use app\lib\phpqrcode\Code;

class Api extends BaseController
{
    function qr()
    {
        Code::create(urldecode(arg('url')),'H',10,APP_PUBLIC."ui".DS.Config::getInstance("frame")->getOne("admin").DS.'img/face.jpg');
    }


}
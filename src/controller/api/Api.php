<?php

namespace app\controller\api;

use app\lib\phpqrcode\Code;

class Api extends BaseController
{
    function qr()
    {
        Code::create(arg('url'),'H',10,APP_IMG.DS.'qrLogo.png');
    }
}
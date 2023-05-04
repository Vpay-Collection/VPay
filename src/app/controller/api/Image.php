<?php
/*******************************************************************************
 * Copyright (c) 2022. Ankio. All Rights Reserved.
 ******************************************************************************/

/**
 * Package: app\controller\api
 * Class Image
 * Created By ankio.
 * Date : 2023/3/20
 * Time : 12:39
 * Description :
 */

namespace app\controller\api;

use cleanphp\base\Variables;
use library\qrcode\Code;

class Image extends BaseController
{
    function qrcode()
    {
        Code::create(urldecode(arg('url', "")), Variables::getStoragePath("logo.jpg"));
    }

}
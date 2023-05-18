<?php
/*******************************************************************************
 * Copyright (c) 2022. Ankio. All Rights Reserved.
 ******************************************************************************/
/**
 * Package: app\controller\api_index
 * Class Main
 * Created By ankio.
 * Date : 2023/5/18
 * Time : 17:56
 * Description :
 */

namespace app\controller\api_index;

use cleanphp\base\Controller;
use cleanphp\base\Route;
use cleanphp\base\Variables;

class Main extends Controller
{
    function robots(): string
    {
        return <<<EOF
User-agent: *

Disallow: /*
EOF;
    }

    function image()
    {
        $file = get('file', '');
        Route::renderStatic(Variables::getStoragePath("uploads", get("type", "temp"), $file));
    }
}
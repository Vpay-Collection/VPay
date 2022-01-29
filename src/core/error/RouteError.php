<?php
/*******************************************************************************
 * Copyright (c) 2022. CleanPHP. All Rights Reserved.
 ******************************************************************************/
/**
 * Class RouteError
 * Created By ankio.
 * Date : 2022/1/12
 * Time : 2:43 下午
 * Description :
 */

namespace app\core\error;

use app\core\debug\Log;
use app\core\web\Response;

class RouteError
{
    public function __construct($message)
    {
        global $__module, $__controller, $__action;
        $nameBase = "app\\controller\\$__module\\BaseController";
        if (!isDebug()) {
            Log::error("route",$message);
            if (method_exists($nameBase, 'err404')) {
                $nameBase::err404($__module, $__controller, $__action, $message);
            } else {
                Response::msg(true, 404, '404 Not Found', '无法找到该页面.', 3, '/');
            }

        } else {
            Error::err($message);
        }
        exitApp($message);
    }
}
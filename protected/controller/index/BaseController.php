<?php
namespace controller\index;
use lib\speed\mvc\Controller;
class BaseController extends Controller
{
    public $layout = "";//模板文件

    public static function err404($module, $controller, $action, $msg)
    {
        header("HTTP/1.0 404 Not Found");
        $obj = new Controller();
        $obj->display("error");
        exit;
    }

    function init()
    {
        session_start();
        header("Content-type: text/html; charset=utf-8");

    }

}

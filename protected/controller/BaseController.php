<?php

class BaseController extends Controller
{
    public $layout = "layout.html";//模板文件

    public static function err404($module, $controller, $action, $msg)
    {
        header("HTTP/1.0 404 Not Found");
        $obj = new Controller();
        $obj->display("error.html");
        exit;
    }

    function init()
    {

        header("Content-type: text/html; charset=utf-8");

    }

    function tips($msg, $url)
    {
        $url = "location.href=\"{$url}\";";
        echo "<html><head><meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\"><script>function sptips(){alert(\"{$msg}\");{$url}}</script></head><body onload=\"sptips()\"></body></html>";
        exit;
    }

    function jump($url, $delay = 0)
    {
        echo "<html><head><meta http-equiv='refresh' content='{$delay};url={$url}'></head><body></body></html>";
        exit;
    }
}

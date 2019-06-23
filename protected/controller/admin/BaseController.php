<?php

/*
 * 后台基类，所有程序的基础
 * */

class BaseController extends Controller
{
    public $layout = "";//模板文件

    public static function err404($module, $controller, $action, $msg)
    {
        header("HTTP/1.0 404 Not Found");
        $obj = new Controller();
        $obj->display("error.html");
        exit;
    }

    function init()
    {
        session_start();
        //header("Content-type: text/html; charset=utf-8");
        $user = new User();

        //var_dump(arg());

        if (!(arg("a") === "Login" || arg("a") === "login") && !$user->islogin()) {

            $this->tips("登录已经失效，请重新登录！", url("main", "index") . "#login");
        }
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

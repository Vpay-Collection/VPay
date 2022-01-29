<?php
/*******************************************************************************
 * Copyright (c) 2022. CleanPHP. All Rights Reserved.
 ******************************************************************************/

namespace app\core\web;

use app\core\cache\Cache;
use app\core\config\Config;
use app\core\error\RouteError;
use app\core\event\EventManager;



/**
 * Class Route
 * @package app\core\web
 * Date: 2020/11/22 11:24 下午
 * Author: ankio
 * Description:路由类
 */
class Route
{


    /**
     * 路由URL生成
     * @param string $m 模块
     * @param string $c 控制器
     * @param string $a 执行方法
     * @param array $params 附加参数
     * @return string
     */
    public static function url(string $m, string $c, string $a, array $params = []): string
    {
        $isRewrite=Config::getInstance("frame")->setLocation(APP_CONF)->getOne("rewrite");
        if(!$isRewrite){
            $params["m"]=$m;
            $params["c"]=$c;
            $params["a"]=$a;

            return Response::getAddress() . "/".(empty($params) ? '' : '?' ). http_build_query($params);
        }

        $paramsStr = empty($params) ? '' : '?' . http_build_query($params);
        $route = "$m/$c/$a";
        $url = Response::getAddress() . "/";
        $default = $url . $route ;
        $default = strtolower($default). $paramsStr;
        Cache::init(365 * 24 * 60 * 60, APP_ROUTE);
        //初始化路由缓存，不区分大小写
        $data = "";
        if (!isDebug())
            $data = Cache::get('route_' . $default);
        if ($data !== "") {
            return $data;
        }


        $arr = str_replace("<m>", $m, $GLOBALS['route']);
        $arr = str_replace("<c>", $c, $arr);
        $arr = str_replace("<a>", $a, $arr);
        $arr = array_flip(array_unique($arr));

        $route_find = $route;
        if (isset($arr[$route])) {


            //处理参数部分
            $route_find = $arr[$route];
            $route_find = str_replace("<m>", $m, $route_find);
            $route_find = str_replace("<c>", $c, $route_find);
            $route_find = str_replace("<a>", $a, $route_find);



            foreach ($params as $key => $val) {
                if (strpos($route_find, "<$key>") !== false) {
                    $route_find = str_replace("<$key>", $val, $route_find);
                    unset($params[$key]);
                }

            }
        }




        if ($route_find == $route || strpos($route_find, '<') !== false) {
            $retUrl = $default;
        } else {
            $paramsStr = empty($params) ? '' : '?' . http_build_query($params);
            $retUrl = $url . $route_find . $paramsStr;
        }
        if (!isDebug())
            Cache::set('route_' . $default, $retUrl);

        return $retUrl;

    }

    /**
     * 路由重写
     */
    public static function rewrite()
    {
        $GLOBALS['route_start']=microtime(true);
        if(isDebug()) $GLOBALS["frame"]["route"][]="路由开始";
        $isRewrite=Config::getInstance("frame")->setLocation(APP_CONF)->getOne("rewrite");
        if(isDebug()) $GLOBALS["frame"]["route"][]="已启用路由重写功能";
        if($isRewrite){
            //不允许的参数
            if (isset($_REQUEST['m']) || isset($_REQUEST['a']) || isset($_REQUEST['c'])) {

                new RouteError("以下参数名不允许：m,a,c!");
            }
            $url = strtolower(urldecode($_SERVER['REQUEST_URI']));
            $data = null;
            if (!isDebug()) {//非调试状态从缓存读取
                Cache::init(365 * 24 * 60 * 60, APP_ROUTE);
                //初始化路由缓存，不区分大小写
                $data = Cache::get($url);
            }
            if ($data !== null && isset($data['real']) && isset($data['route'])) {
                if(isDebug()) $GLOBALS["frame"]["route"][]="已确认路由缓存有效";
                $route_arr_cp = $data['route'];
            } else {
                if(isDebug()) $GLOBALS["frame"]["route"][]="无有效路由缓存";
                $route_arr = self::convertUrl();
                if (!isset($route_arr['m']) || !isset($route_arr['a']) || !isset($route_arr['c'])) {
                    new RouteError("错误的路由! 我们需要至少三个参数.");
                }
                $route_arr = array_merge($_GET, $route_arr);//get中的参数直接覆盖
                $route_arr_cp = $route_arr;
                //重写缓存表
                $__module = ($route_arr['m']);
                unset($route_arr['m']);

                $__controller = ($route_arr['c']);
                unset($route_arr['c']);

                $__action = ($route_arr['a']);
                unset($route_arr['a']);

                $nowUrl=urldecode(Response::getNowAddress());
                $defineUrl=urldecode(url($__module, $__controller, $__action, $route_arr));
                if (strtolower($defineUrl)!== strtolower($nowUrl)) {
                    new RouteError("错误的路由，该路由已被定义.\n当前地址:" . $nowUrl . '  定义的路由为:' . $defineUrl.",您应当通过【定义的路由】进行访问。");
                }

                $real = "$__module/$__controller/$__action";
                if (sizeof($route_arr)) {
                    $real .= '?' . http_build_query($route_arr);
                }
                $arr = [
                    'real' => $real,
                    'route' => $route_arr_cp,
                ];
                if (!isDebug())
                    Cache::set($url, $arr);

            }
        }else{
            if(!isset($_REQUEST['m']))$_GET["m"]="index";
            if(!isset($_REQUEST['a']))$_GET["a"]="index";
            if(!isset($_REQUEST['c']))$_GET["c"]="main";
            $route_arr_cp=[];
        }

        $_REQUEST = array_merge($_GET, $_POST, $route_arr_cp);

        global $__module, $__controller, $__action;
        $__module = $_REQUEST['m'];
        $__controller = $_REQUEST['c'];
        $__action = $_REQUEST['a'];
        if(isDebug()) $GLOBALS["frame"]["route"][]="路由完成";
        self::isInstall();
        EventManager::fire("afterRoute", [$__module, $__controller, $__action]);
    }

    /**
     * 路由匹配
     * @return array
     */
    public static function convertUrl(): array
    {

        $route_arr = [];

        $url = strtolower($GLOBALS['http_scheme'] . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);

        if(isDebug()) $GLOBALS["frame"]["route"][]="正在匹配路由表:".$url;

        if (strpos($url, '?') !== false) {
            $url = substr($url, 0, strpos($url, '?'));
        }

        foreach ($GLOBALS['route'] as $rule => $mapper) {
            if(isDebug()) $GLOBALS["frame"]["route"][]="尝试匹配：".$rule;
            $rule = Response::getAddress() . '/' . $rule;
            $rule = strtolower($rule);
            $rule = '/' . str_ireplace(
                    ['\\\\', $GLOBALS['http_scheme'], '/', '<', '>', '.'],
                    ['', '', '\/', '(?P<', '>[\x{4e00}-\x{9fa5}a-zA-Z0-9_\.-]+)', '\.'], $rule) . '$/u';
            
            if (preg_match($rule, $url, $matchs)) {
                $route = explode("/", $mapper);
                if (isset($route[2])) {
                    [$route_arr['m'], $route_arr['c'], $route_arr['a']] = $route;
                }
                foreach ($matchs as $matchkey => $matchval) {
                    if (!is_int($matchkey)) $route_arr[$matchkey] = $matchval;
                }
                if(isDebug()) $GLOBALS["frame"]["route"][]="已匹配路由：".print_r($rule,true);
                break;
            }

        }
        if(isDebug()){
            $GLOBALS["frame"]["route"][]="最终路由数据：".print_r($route_arr,true);
            $GLOBALS["frame"]["time"]["route_time"]=(microtime(true)-$GLOBALS['route_start']);
        }
        return $route_arr;
    }
    /**
     *  判断是否有安装程序，有就跳转
     */
    private static function isInstall(){

        if($GLOBALS["frame"]["install"]!==""&&!is_file(APP_CONF.'install.lock')){
            global $__module;
            if($__module===$GLOBALS["frame"]["install"])return;

            //没有锁
            Response::location(self::url($GLOBALS["frame"]["install"], "main", "index"));
        }
    }

}






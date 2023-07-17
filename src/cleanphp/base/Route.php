<?php
/*
 * Copyright (c) 2023. Ankio. All Rights Reserved.
 */

namespace cleanphp\base;


use cleanphp\App;
use cleanphp\engine\EngineManager;
use cleanphp\file\Log;

/**
 * Class Route
 * @package cleanphp\web
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

        $route = "$m/$c/$a";

        $param_str = empty($params) ? '' : '?' . http_build_query($params);


        $url = Request::getAddress() . "/";

        $default = $url . $route;
        $default = strtolower($default) . $param_str;


        $array = str_replace("<m>", $m, Config::getRouteTable());
        $array = str_replace("<c>", $c, $array);
        $array = str_replace("<a>", $a, $array);
        $array = array_flip(array_unique($array));

        //var_dump($array,$route);
        $route_find = $route;
        if (isset($array[$route])) {
            //处理参数部分
            $route_find = $array[$route];
            $route_find = str_replace("<m>", $m, $route_find);
            $route_find = str_replace("<c>", $c, $route_find);
            $route_find = str_replace("<a>", $a, $route_find);


            foreach ($params as $key => $val) {
                if (str_contains($route_find, "<$key>")) {
                    $route_find = str_replace("<$key>", $val, $route_find);
                    unset($params[$key]);
                }

            }
        }


        if ($route_find === $route || str_contains($route_find, '<')) {
            $ret_url = $default;
        } else {

            $param_str = empty($params) ? '' : '?' . http_build_query($params);
            $ret_url = $url . $route_find . $param_str;
        }

        if (strrpos($ret_url, "?") === strlen($ret_url) - 1) {
            return substr($ret_url, 0, strlen($ret_url) - 1);
        }
        return trim($ret_url, "/");

    }

    /**
     * 路由重写
     */
    public static function rewrite(): array
    {
        $__route_start__= microtime(true);

        $url = self::getQuery();
        EventManager::addListener("__route_before__",function ($event,&$data){
            $__route_start__= microtime(true);
            if (str_starts_with($data,'clean_static')) {
                $uri = str_replace('clean_static', "", $data);
                $path = Variables::setPath(APP_DIR, 'app' , "public", str_replace("..", ".", $uri));
                $t = round((microtime(true) - $__route_start__) * 1000, 4);
                App::$route = $t;
                self::renderStatic($path);
            }
        });

        EventManager::trigger("__route_before__", $url);//路由之前

        $array = self::parseUrl($url);

        if (!isset($array['m']) || !isset($array['a']) || !isset($array['c'])) {
            EngineManager::getEngine()->onNotFound("路由失败，未能找到对应资源:" . $url);
        }


        EventManager::trigger("__route_end__", $array);//路由之后

        $__module = $array['m'];
        $__controller = ($array['c']);
        $__action = ($array['a']);


        App::$debug && Log::record("Route", sprintf("路由结果：%s/%s/%s", $__module, $__controller, $__action));

        unset($array['m']);
        unset($array['c']);
        unset($array['a']);

        $_GET = array_merge($_GET, $array);

        $t = round((microtime(true) - $__route_start__) * 1000, 4);
        App::$route = $t;
        App::$debug && Log::record("Route", sprintf("路由总耗时：%s 毫秒",$t) , Log::TYPE_WARNING);

        return [$__module, $__controller, $__action];
    }

    private static string $__route_query__ = "";
    /**
     * 获取路径
     * @return string
     */
    public static function getQuery(): string
    {
        if (self::$__route_query__ === "") {
            $query = $_SERVER['REQUEST_URI'] ?? "/";
            $query = strtok($query, '?');
            $query = trim($query, "/");
            $query = $query === "" ? "/" : $query;
            self::$__route_query__ = $query;
        }

        return self::$__route_query__ ;
    }



    /**
     * 渲染静态资源
     * @param $path
     * @return void
     */
    public static function renderStatic($path): void
    {
        if (is_file($path)) {
            $type = file_type($path);

            (new Response())->render(self::replaceStatic($path))->code(200)->contentType($type)->send();
        } else {
            EngineManager::getEngine()->onNotFound("找不到指定的静态资源：". $path);
        }
    }

    /**
     * 替换静态文件
     * @param string $content
     * @return string
     */
    public static function replaceStatic(string $content): string
    {
        $replaces = Variables::get("__static_replace__", "../../public");
        return str_replace($replaces, "/clean_static", $content);
    }

    /**
     * 路由匹配
     * @param string|null $query
     * @return array
     */
    public static function parseUrl(string $query = null): array
    {
        $array = [];
        if ($query === null) {
            $query = self::getQuery();
        }

        $routeTable = Config::getRouteTable();
        $lowercaseRoutes = array_change_key_case($routeTable, CASE_LOWER);
        $debugEnabled = App::$debug;

        $debugEnabled && Log::record("Route", sprintf("路由地址：%s", $query));
        // 修改匹配最大时间
        ini_set('pcre.recursion_limit', 200);

        foreach ($lowercaseRoutes as $rule => $mapper) {
            $debugEnabled && Log::record("Route", sprintf("路由匹配：%s => %s", $rule, $mapper));
            $rule = '@^' . str_ireplace(
                    ['\\\\', '/', '<', '>', '.'],
                    ['', '\/', '(?P<', '>[\x{4e00}-\x{9fa5}a-zA-Z0-9_\.\-\/]+)', '\.'],
                    strtolower($rule)
                ) . '$@ui';
            if (preg_match($rule, $query, $matches)) {
                $route = explode("/", trim($mapper));
                if (isset($route[2])) {
                    [$array["m"], $array["c"], $array["a"]] = $route;
                }
                foreach ($matches as $k => $v) {
                    if (is_string($k)) {
                        $array[$k] = $v;
                    }
                }
                break;
            }
        }

        return $array;
    }


}






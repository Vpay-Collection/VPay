<?php
/*
 * Copyright (c) 2023. Ankio. All Rights Reserved.
 */

/**
 * Package: cleanphp\base
 * Class Variables
 * Created By ankio.
 * Date : 2022/11/9
 * Time : 20:56
 * Description :
 */

namespace cleanphp\base;


class Variables
{
    private static string $version = "4.0.1";
    public static string $site_name = ""; //站点模块名称
    /**
     * @var string[] 内部变量
     */
    public static array $inner_arrays = [];

    public static function getSite($split = DS): string
    {
        if (self::$site_name == "") return "";
        else return self::$site_name . $split;
    }

    public static function getVersion(): string
    {
        return self::$version;
    }

    public static function init(): void
    {

        $app_dir = APP_DIR . DS . 'app' . DS . self::getSite();

        self::$inner_arrays = [
            "path_app" => $app_dir,
            "path_storage" => $app_dir . 'storage' . DS,
            "path_cache" => $app_dir . 'storage' . DS . 'cache' . DS,
            "path_logs" => $app_dir . 'storage' . DS . 'logs' . DS,
            "path_db" => $app_dir . 'database' . DS,
            "path_controller" => $app_dir . 'controller' . DS,
            "path_view" => $app_dir . 'view' . DS,
            "path_lib" => APP_DIR . DS . 'library' . DS,
            "path_public" => APP_DIR . DS . 'public' . DS,
            "path_cleanphp" => APP_DIR . DS . 'cleanphp' . DS,
        ];

        if (!is_writable(APP_DIR)) {
            exit("[ CleanPHP ] 环境异常：请检查程序目录和程序父级目录权限是否具有可写权限!");
        }
    }

    /**
     * 获取变量
     * @param string $key
     * @param null $default 默认值
     * @return mixed|null
     */
    public static function get(string $key, $default = null)
    {
        return $GLOBALS[$key] ?? $default;
    }

    /**
     * 设置变量
     * @param string $key
     * @param $value
     * @return void
     */
    public static function set(string $key, $value)
    {
        $GLOBALS[$key] = $value;
    }

    /**
     * 删除变量
     * @param string $key
     * @return void
     */
    public static function del(string $key)
    {
        if (isset($GLOBALS[$key])) unset($GLOBALS[$key]);
    }

    /**
     * 获取控制器路径
     * @param string ...$path
     * @return string
     */
    public static function getControllerPath(string ...$path): string
    {
        return self::setPath(self::getInner("path_controller"), ...$path);
    }

    /**
     * 构造路径
     * @param string $start
     * @param ?array $args
     * @return string
     */
    public static function setPath(string $start, ...$args): string
    {
        $ret = '';
        foreach ($args as $k => $v)
            if (is_string($v)) {
                if ($k == 0) {
                    $ret .= $v;
                } else {
                    $ret .= DS . $v;
                }
            }

        return str_replace(DS . DS, DS, $start . DS . $ret);
    }

    /**
     * 获取内部变量
     * @param $key
     * @return string
     */
    private static function getInner($key): string
    {
        return self::$inner_arrays[$key] ?? "";
    }

    /**
     * 获取存储文件的路径
     * @param string ...$path
     * @return string
     */
    public static function getStoragePath(string ...$path): string
    {
        return self::setPath(self::getInner("path_storage"), ...$path);
    }

    /**
     * 获取缓存路径
     * @param string ...$path
     * @return string
     */
    public static function getCachePath(string ...$path): string
    {
        return self::setPath(self::getInner("path_cache"), ...$path);
    }

    /**
     * 获取日志路径
     * @param string ...$path
     * @return string
     */
    public static function getLogPath(string ...$path): string
    {

        return self::setPath(self::getInner("path_logs"), ...$path);
    }


    /**
     * 获取模型路径
     * @param string ...$path
     * @return string
     */
    public static function getDbPath(string ...$path): string
    {
        return self::setPath(self::getInner("path_db"), ...$path);
    }

    /**
     * 获取第三方库的路径
     * @param string ...$path
     * @return string
     */
    public static function getLibPath(string ...$path): string
    {
        return self::setPath(self::getInner("path_lib"), ...$path);
    }


    /**
     * 获取视图路径
     * @param string ...$path
     * @return string
     */
    public static function getViewPath(string ...$path): string
    {
        return self::setPath(self::getInner("path_view"), ...$path);
    }

    /**
     * 获取App路径
     * @param string ...$path
     * @return string
     */
    public static function getAppPath(string ...$path): string
    {
        return self::setPath(self::getInner("path_app"), ...$path);
    }


    /**
     * push一个数据到全局数组中的变量
     * @param $var
     * @param $data
     * @return void
     */
    public static function push($var,$data): void
    {
        if(isset($GLOBALS[$var]) && is_array($GLOBALS[$var])){
            $GLOBALS[$var][] = $data;
        }
    }

    /**
     * 对全局数组中的整数进行加法操作
     * @param $var
     * @param int $count
     * @return void
     */
    public static function plus($var, int $count = 1): void
    {
        if(isset($GLOBALS[$var]) && is_int($GLOBALS[$var])){
            $GLOBALS[$var] = $GLOBALS[$var] + $count;
        }else{
            $GLOBALS[$var] =  $count;
        }
    }

    /**
     *  对全局数组中的整数进行减法操作
     * @param $var
     * @param $count
     * @return void
     */
    public static function minus($var,$count): void
    {
        if(isset($GLOBALS[$var]) && is_int($GLOBALS[$var])){
            $GLOBALS[$var] = $GLOBALS[$var] - $count;
        }else{
            $GLOBALS[$var] = - $count;
        }
    }


}
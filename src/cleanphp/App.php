<?php
/*
 * Copyright (c) 2023. Ankio. All Rights Reserved.
 */

/**
 * Package: core
 * Class App
 * Created By ankio.
 * Date : 2022/11/9
 * Time : 12:40
 * Description :
 */

namespace cleanphp;

use cleanphp\base\Config;
use cleanphp\base\Controller;
use cleanphp\base\Error;
use cleanphp\base\EventManager;
use cleanphp\base\Loader;
use cleanphp\base\MainApp;
use cleanphp\base\Request;
use cleanphp\base\Response;
use cleanphp\base\Route;
use cleanphp\base\Variables;
use cleanphp\engine\CliEngine;
use cleanphp\engine\EngineManager;

use cleanphp\exception\ExitApp;

use cleanphp\file\Log;
use cleanphp\process\Async;


class App
{
    public static bool $debug = false;//是否调试模式
    public static bool $cli = false;//是否命令行模式
    public static bool $exit = false;//标记是否退出运行
    /**
     * @var $app ?MainApp
     */
    private static ?MainApp $app = null;

    /**
     * @param bool $debug
     */
    static function run(bool $debug = false)
    {
        App::$debug = $debug;
        error_reporting(E_ALL & ~(E_STRICT | E_NOTICE));
        ini_set("display_errors", "Off");//禁用错误提醒
        if (version_compare(PHP_VERSION, '7.4.0', '<')) {
            exit("请使用PHP 7.4以上版本运行该应用");
        }
        self::$cli = PHP_SAPI === 'cli';
        if (self::$cli) {
            $_SERVER["SERVER_NAME"] = "0.0.0.0";
            $_SERVER["REQUEST_METHOD"] = "GET";
        }//命令行重置
        define("DS", DIRECTORY_SEPARATOR);//定义斜杠符号
        define("APP_CORE", APP_DIR . DS . 'cleanphp' . DS);//定义程序的核心目录
        include_once APP_CORE . "helper.php";//载入内置助手函数
        include_once APP_CORE . "base" . DS . "Variables.php";// 加载变量
        include_once APP_CORE . "base" . DS . "Loader.php";// 加载自动加载器


        Variables::init();//初始化变量
        //初始化时间
        App::$debug && Variables::set('__frame_start__', microtime(true));
        try {

            Loader::register();// 注册自动加载
            App::$debug && Log::record("Frame", "框架启动...");
            Config::register();// 加载配置文件
            if (self::$cli) {

                EngineManager::setDefaultEngine(new CliEngine());
            }
            if (self::$debug) {
                if (self::$cli)
                    Log::record("Request", "命令行启动框架", Log::TYPE_WARNING);
                else {
                    Log::record("Request", $_SERVER["REQUEST_METHOD"] . " " . $_SERVER["REQUEST_URI"]);
                    foreach (Request::getHeaders() as $key => $v) {
                        Log::record("Headers", " [ $key ] => $v");
                    }
                    if (Request::isPost()) {
                        Log::record("Post", file_get_contents('php://input'));
                    }
                }

            }
            Error::register();// 注册错误和异常处理机制
            //Application实例化
            $app = "\app\\" . Variables::getSite("\\") . "Application"; //入口初始化
            if (class_exists($app) && ($imp = class_implements($app)) && in_array(MainApp::class, $imp)) {
                self::$app = new $app();
                self::$app->onFrameworkStart();
            }
            Async::register();//异步任务注册
            EventManager::trigger("__frame_init__");//框架初始化
            //清除缓存
            App::$debug && self::cleanCache();
            //路由
            [$__module, $__controller, $__action] = Route::rewrite();

            //模块检查
            Variables::set("__request_module__", $__module);
            Variables::set("__request_controller__", $__controller);
            Variables::set("__request_action__", $__action);
            //通过路由检测后才认为是请求到达
            self::$app && self::$app->onRequestArrive();
            EventManager::trigger("__application_init__");//框架初始化
            if (!is_dir(Variables::getControllerPath($__module))) {
                EngineManager::getEngine()->onNotFound("模块 '$__module' 不存在!");
            }
            // 控制器检查
            if (strtolower($__controller) === 'basecontroller')
                Error::err("基类 'BaseController' 不允许被访问！", [], "Controller");

            $controller_name = ucfirst($__controller);

            $controller_class = 'app\\' . Variables::getSite("\\") . 'controller\\' . $__module . '\\' . $controller_name;

            if (!class_exists($controller_class, true)) {
                $data = [$__module, $__controller, $__action, $controller_class];
                EventManager::trigger("__not_render__", $data);
                EngineManager::getEngine()->onNotFound("模块 ( $__module ) => 控制器 ( $controller_name ) 不存在!");
            }


            $method = method_exists($controller_class, $__action);
            if (!$method) {
                $data = [$__module, $__controller, $__action, $controller_class];
                EventManager::trigger("__not_render__", $data);
                EngineManager::getEngine()->onNotFound("模块 ( $__module ) => 控制器 ( $controller_name ) 中的方法 ( $__action ) 不存在!");
            }

            if (!in_array_case($__action, get_class_methods($controller_class)) || $__action === '__init') {
                Error::err("模块 ( $__module ) => 控制器 ( $controller_name ) 中的方法 ( $__action ) 为私有方法，禁止访问!", [], "Action");
            }

            /**
             * @var $controller_obj Controller
             */

            $controller_obj = new $controller_class();

            $result = $controller_obj->$__action();
            if ($result !== null)
                (new Response())->render($result, $controller_obj->getCode(), EngineManager::getEngine()->getContentType())->send();
            else {
                $data = [$__module, $__controller, $__action, $controller_class];
                EventManager::trigger("__not_render__", $data);
                EngineManager::getEngine()->onNotFound("No data.");
            }

        } catch (ExitApp $exit_app) {//执行退出
            App::$debug && Log::record("Frame", sprintf("框架执行退出: %s", $exit_app->getMessage()));
        } catch (\Exception|\Error $exception) {
            Error::err($exception->getMessage(), $exception->getTrace());
        } finally {
            self::$app && self::$app->onRequestEnd();
            if (App::$debug) {
                Log::record("Frame", "框架响应结束...");
                $t = round((microtime(true) - Variables::get("__frame_start__", 0)) * 1000, 2);
                Log::record("Frame", sprintf("会话运行时间：%s 毫秒", $t), Log::TYPE_WARNING);
                if ($t > 100) {
                    Log::record("Frame", sprintf("优化提醒：您的当前应用会话处理用时（%s毫秒）超过 100 毫秒，建议对代码进行优化以获得更好的使用体验。", $t), Log::TYPE_WARNING);
                }
            }
        }
    }

    /**
     * 退出会话
     * @param $msg
     * @param bool $output 直接输出
     * @return void
     */
    static function exit($msg, bool $output = false)
    {
        if (self::$exit) return; //防止一个会话中重复抛出exit异常
        self::$exit = true;
        if ($output) echo $msg;
        throw new ExitApp($msg);
    }

    /**
     * 清除缓存文件
     * @return void
     */
    static function cleanCache()
    {
        //清除opcache
        if (function_exists('opcache_reset')) {
            opcache_reset();
        }
    }


}
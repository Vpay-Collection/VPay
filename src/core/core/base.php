<?php
/*******************************************************************************
 * Copyright (c) 2022. CleanPHP. All Rights Reserved.
 ******************************************************************************/

namespace app\core\core;
use app\core\config\Config;
use app\core\error\Error;
use app\core\event\EventManager;

// 全局变量清空
//框架加载的开始时间
$GLOBALS['frame_start'] = microtime(true);
//定义使用的时区
date_default_timezone_set('PRC');
//定义框架版本
define('FRAME_VERSION', '3.0');
//定义控制器所在路径
define('APP_CONTROLLER', APP_DIR.DS.'controller'.DS);
//定义存储空间位置
define('APP_STORAGE', APP_DIR.DS.'storage'.DS);
//渲染完成的视图文件
define('APP_TMP', APP_STORAGE.'view'.DS);
//缓存文件
define('APP_CACHE', APP_STORAGE.'cache'.DS);
//路由缓存文件
define('APP_ROUTE', APP_STORAGE.'route'.DS);
//日志文件
define('APP_LOG', APP_STORAGE.'logs'.DS);
//垃圾文件
define('APP_TRASH', APP_STORAGE.'trash'.DS);
//框架拓展目录
define('APP_EXTEND', APP_DIR.DS.'extend'.DS);
//框架配置目录
define('APP_CONF', APP_DIR.DS.'config'.DS);
//模块路径
define('APP_MODEL',APP_DIR.DS.'model'.DS);
//框架第三方库
define('APP_LIB', APP_DIR.DS.'lib'.DS);
//框架原始视图存储位置
define('APP_VIEW', APP_DIR.DS.'static'.DS.'view'.DS);
//框架原始视图（内置Response皮肤）存储位置
define('APP_INNER', APP_DIR.DS.'static'.DS.'innerView'.DS);
//框架公开位置
define('APP_PUBLIC', APP_DIR.DS.'public'.DS);
//UI位置（可以被直接访问）
define('APP_UI', APP_DIR.DS.'public'.DS.'ui'.DS);
//appComposer
define('APP_COMPOSER', APP_DIR.DS.'vendor'.DS);
////vendor/autoload.php
//载入内置助手函数
require APP_CORE."core".DS."helper.php";
// 载入自动加载类
require APP_CORE."core".DS."Loader.php";
// 注册自动加载
Loader::register();
// 注册错误和异常处理机制
Error::register();
// 加载配置文件
Config::register();
// 事件模型注册
EventManager::register();

EventManager::fire("beforeRunFrame", null);

if(isDebug()) {
    $GLOBALS["frame"]["time"]["time"]=date("Y-m-d H:i:s");
    $GLOBALS["frame"]["time"]["tpl_time"]=0;
    $GLOBALS["frame"]["clean"][]="框架启动";
}






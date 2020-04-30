<?php

namespace app;
use app\config\Config;
date_default_timezone_set('PRC');
define('FARME_VERSION', '4.0.2');
define('APP_TMP', APP_DIR . DS . 'protected' . DS . 'tmp' . DS);
define('APP_LIB', APP_DIR . DS . 'protected' . DS . 'lib' . DS);
define('APP_VIEW', APP_DIR . DS . 'protected' . DS . 'view' . DS);
define('APP_LOG', APP_DIR . DS . 'protected' . DS . 'logs' . DS);
define('APP_I', APP_DIR . DS . 'i' . DS);

//载入内置函数
require APP_CORE . "Function.php";
// 载入Loader类
require APP_CORE . "Loader.php";
// 注册自动加载
Loader::register();
// 加载配置文件
Config::register();
// 注册错误和异常处理机制
Error::register();



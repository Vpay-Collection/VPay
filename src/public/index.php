<?php
/*******************************************************************************
 * Copyright (c) 2020. CleanPHP. All Rights Reserved.
 ******************************************************************************/

use app\core\core\Clean;
//定义运行根目录
define('APP_DIR', dirname(dirname(__FILE__)));
//定义斜杠符号
define('DS', DIRECTORY_SEPARATOR);
//定义程序的核心目录
define('APP_CORE', APP_DIR . DS . 'core' . DS);
//载入基础函数
require_once(APP_CORE."core".DS."base.php");
//框架启动
Clean::Run();

<?php
/*******************************************************************************
 * Copyright (c) 2022. CleanPHP. All Rights Reserved.
 ******************************************************************************/
declare(strict_types=1);
ignore_user_abort(true);
$GLOBALS['__frame_start__'] = microtime(true);
$GLOBALS['__memory_start__'] = memory_get_usage();

use cleanphp\App;

if (version_compare(PHP_VERSION, '8.2.0', '<')) {
    exit("[ CleanPHP ] 环境异常：请使用PHP 8.2 以上版本运行该应用");
}
define('APP_DIR', dirname(__FILE__,2));//定义运行根目录
include dirname(__FILE__)."/App.php";
App::run();

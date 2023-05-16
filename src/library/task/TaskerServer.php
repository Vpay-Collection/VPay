<?php
/*******************************************************************************
 * Copyright (c) 2022. CleanPHP. All Rights Reserved.
 ******************************************************************************/

namespace library\task;

use cleanphp\App;
use cleanphp\cache\Cache;
use cleanphp\file\Log;


/**
 * Class Server
 * @package extend\net_ankio_tasker\core
 * Date: 2020/12/31 09:57
 * Author: ankio
 * Description:Tasker服务
 */
class TaskerServer
{

    /**
     * 启动任务扫描服务
     * @return void
     */
    public static function start()
    {
        if (empty(Cache::init(20)->get("task"))) {//没有锁定，请求保持锁定
            App::$debug && Log::record("Tasker", "定时任务进程未锁定，下发任务");
            go(function () {
                App::$debug && Log::record("Tasker", "定时任务进程启动");
                do {
                    Cache::init(15)->set("task", getmypid());//更新锁定时间
                    TaskerManager::run();
                    sleep(10);
                    if (Cache::init(15)->get("task") !== getmypid()) {
                        App::$debug && Log::record("Tasker", "定时任务进程发生变化，当前进程结束");
                        break;
                    }
                } while (true);
               // Cache::init()->del("task");
            }, 0);
        } else {
            App::$debug && Log::record("Tasker", "定时任务进程已锁定，不处理定时任务");
        }
    }

    //停止任务
    public static function stop()
    {
        Cache::init()->del("task");
    }

}
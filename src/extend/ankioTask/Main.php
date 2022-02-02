<?php
/*******************************************************************************
 * Copyright (c) 2022. CleanPHP. All Rights Reserved.
 ******************************************************************************/

namespace app\extend\ankioTask;

use app\core\debug\Log;
use app\extend\ankioTask\core\Server;
use app\core\event\EventListener;

define("EXTEND_TASKER",APP_EXTEND."ankioTask".DS);

class Main implements EventListener
{
    /**
     * @param string $event
     * @param array|string $msg
     * @return void 实现接口
     */
    public function handleEvent(string $event, $msg)
    {
        $server=Server::getInstance();//获取对象实例
        $server->route();
        $server->start();//启动服务
    }
}

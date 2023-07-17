<?php
/*******************************************************************************
 * Copyright (c) 2022. Ankio. All Rights Reserved.
 ******************************************************************************/
/**
 * File autoload.php
 * Created By ankio.
 * Date : 2022/11/19
 * Time : 17:01
 * Description : 自动加载器，注册事件
 */

//此处建议注册事件，而不是直接执行class，因为此处的包含时机比Config注册的时机还要早。

use cleanphp\App;
use cleanphp\base\EventManager;
use cleanphp\base\Session;
use library\task\TaskerServer;

if(!App::$cli && Session::getInstance()->get("__tasker_server__",false) ) {
    EventManager::addListener("__frame_init__", function ($event, &$data) {

        Session::getInstance()->set("__tasker_server__",true,3600);
        TaskerServer::start();
});
}


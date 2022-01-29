<?php
/*******************************************************************************
 * Copyright (c) 2022. CleanPHP. All Rights Reserved.
 ******************************************************************************/

/**
 * 事件管理器
 * Class EventManager
 */

namespace app\core\event;

use app\core\utils\StringUtil;

/**
 * Class EventManager
 * @package app\core\event
 * Date: 2020/11/20 12:13 上午
 * Author: ankio
 * Description: 事件管理器
 */
class EventManager
{
    private static array $eventList = [];

    /**
     * 事件注册，主要是注册给拓展使用
     */
    public static function register()
    {
        $data = scandir(APP_EXTEND);
        foreach ($data as $value) {
            if (!StringUtil::get($value)->startsWith(".")) {
                $file = APP_EXTEND  . $value . DS . 'register.php';
                if (file_exists($file))
                    include_once $file;

            }
        }
    }


    /**
     * 绑定事件
     * @param string $eventName 事件名
     * @param string $listener 监听器名
     */
    public static function attach(string $eventName, string $listener)
    {
        //一个事件名绑定多个监听器
        self::$eventList[$eventName][] = $listener;
    }


    /**
     * 删除事件
     * @param $eventName
     */
    public static function detach($eventName)
    {
        unset(self::$eventList[$eventName]);
    }

    /**
     * 触发事件
     * @param string $eventName  事件名
     * @param  array    $data       事件携带的数据
     */
    public static function fire(string $eventName, $data=[])
    {
        foreach (self::$eventList as $attachEventName => $listenerList) {
            //匹配监听列表
            if ($eventName == $attachEventName) {
                foreach ($listenerList as $eventListener) {
                    (new $eventListener())->handleEvent($eventName,$data);
                }
            }
        }
    }
}


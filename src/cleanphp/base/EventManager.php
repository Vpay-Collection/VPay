<?php
/*
 * Copyright (c) 2023. Ankio. All Rights Reserved.
 */

/**
 * 事件管理器
 * Class EventManager
 */

namespace cleanphp\base;


use cleanphp\App;
use cleanphp\file\Log;

/**
 * Class EventManager
 * @package cleanphp\event
 * Date: 2020/11/20 12:13 上午
 * Author: ankio
 * Description: 事件管理器
 */
class EventManager
{
    private static array $events = [];

    /**
     * 监听事件
     * @param string $event_name 事件名
     * @param callable $func 匿名函数，接受两个参数：$func($event_name, $data)
     * @param int $level 事件等级
     */
    public static function addListener(string $event_name, callable $func, int $level = 1000): void
    {
        if (!isset(self::$events[$event_name])) {
            self::$events[$event_name] = [];
        }

        while (array_key_exists($level, self::$events[$event_name])) {
            $level++;
        }

        self::$events[$event_name][$level] = $func;
        App::$debug && Log::record("Event", "注册事件：$event_name ,优先级：$level");
    }


    /**
     * 删除事件
     * @param $event_name
     */
    public static function removeListener($event_name): void
    {
        unset(self::$events[$event_name]);
        App::$debug && Log::record("Event", "移除事件：$event_name ");
    }

    /**
     * 触发事件
     * @param string $event_name 事件名
     * @param mixed|null    &$data 事件携带的数据
     * @param bool $once 只获取一个有效返回值
     */
    public static function trigger(string $event_name, mixed &$data = null, bool $once = false)
    {
        if (!array_key_exists($event_name, self::$events)) {
            return null;
        }

        $list = self::$events[$event_name];
        $results = [];

        foreach ($list as $key => $event) {
            App::$debug && Log::record("Event", "事件响应：$event_name ");
            $results[$key] = $event($event_name, $data);

            if (false === $results[$key] || (!is_null($results[$key]) && $once)) {
                break;
            }
        }

        return $once ? end($results) : $results;
    }

    public static function list(): array
    {
        return self::$events;
    }
}

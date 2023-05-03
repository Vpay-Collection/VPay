<?php
/*******************************************************************************
 * Copyright (c) 2022. CleanPHP. All Rights Reserved.
 ******************************************************************************/

namespace library\task;

use cleanphp\App;
use cleanphp\base\Variables;
use cleanphp\cache\Cache;
use cleanphp\file\Log;

/**
 * Class Tasker
 * @package extend\net_ankio_tasker\core
 * Date: 2020/12/23 23:46
 * Author: ankio
 * Description: 定时任务管理器
 */
class TaskerManager
{
    /**
     * 获取定时任务列表
     * @return array|mixed
     */
    public static function list()
    {
        $list = Cache::init(0, Variables::getCachePath("tasker", DS))->get("tasker_list");
        if (empty($list)) {
            return [];
        } else {
            return $list;
        }
    }

    /**
     * 获取指定的定时任务
     * @param $key
     * @return TaskInfo|null
     */
    private static function get($key): ?TaskInfo
    {
        $list = self::list();
        /**
         * @var $value TaskInfo
         */
        foreach ($list as $value) {
            if ($key === $value->key) return $value;
        }
        return null;
    }


    /**
     * 清空所有定时任务
     * @return void
     */
    public static function clean()
    {
        Cache::init(0, Variables::getCachePath("tasker", DS))->del("tasker_list");
    }

    /**
     * 获取执行时间
     * @param $key
     * @return int
     */
    private static function getTimes($key): int
    {
        $task = self::get($key);
        if (!$task) {
            return 1 - $task->times;
        }
        return 0;
    }


    /**
     * 删除指定ID的定时任务
     * @param $key
     * @return void
     */
    public static function del($key)
    {
        $list = self::list();
        /**
         * @var $value TaskInfo
         */
        foreach ($list as &$value) {
            if ($key === $value->key) {
                unset($value);
                break;
            }
        }
        Cache::init(0, Variables::getCachePath("tasker", DS))->set("tasker_list", $list);
    }


    /**
     * 添加一个定时任务，与linux定时任务语法完全一致
     * @param array $package 定时任务时间包，使用{@link TaskerTime}来指定
     * @param TaskerAbstract $taskerAbstract 需要运行的定时任务，需要继承{@link TaskerAbstract}类并实现{@link TaskerAbstract::onStart()}方法
     * @param string $name 定时任务名称
     * @param int $times 定时任务的执行次数，只有当{@link $loop}参数为true的时候，执行次数才会生效
     * @param bool $loop 是不是为循环定时任务
     * 返回定时任务ID
     * @return false|string
     */
    public static function add(array $package, TaskerAbstract $taskerAbstract, string $name, int $times = -1, bool $loop = false)
    {
        if (sizeof($package) != 5) return false;
        [$minute, $hour, $day, $month, $week] = $package;
        $time = self::getNext($minute, $hour, $day, $month, $week, $loop ? 1 : 0);
        $task = new TaskInfo();
        $task->times = $times;
        $task->name = $name;
        $task->minute = $minute;
        $task->hour = $hour;
        $task->day = $day;
        $task->month = $month;
        $task->week = $week;
        $task->times = $times;
        if (!$loop) $task->times = -1;
        $task->loop = $loop;
        $task->key = uniqid("task_");
        $task->closure = $taskerAbstract;
        $list = self::list();
        $list[] = $task;
        Cache::init(0, Variables::getCachePath("tasker", DS))->set("tasker_list", $list);
        if (App::$debug) {
            Log::record("Tasker", "添加定时任务：$name => " . get_class($taskerAbstract));
            Log::record("Tasker", "初次添加后，执行时间为：" . date("Y-m-d H:i:s", $time));
        }
        return $task->key;
    }

    /**
     * 执行一次遍历数据库
     * @return void
     */
    public static function run()
    {

        $data = self::list();
        /**
         * @var $value TaskInfo
         */
        foreach ($data as $k => $value) {
            //循环并且次序=0
            if ($value->times === 0) {
                App::$debug && Log::record("Tasker", "该ID ({$value->name})[{$value->key}] 的定时任务执行完毕");
                unset($data[$k]);
            } elseif ($value->next <= time()) {
                $time = self::getNext($value->minute, $value->hour, $value->day, $value->month, $value->week, $value->loop);
                $value->next = $time;
                $value->times--;
                App::$debug && Log::record("Tasker", "执行完成后，下次执行时间为：" . date("Y-m-d H:i:s", $time));
                /**
                 * @var  TaskerAbstract $task
                 */
                $task = $value->closure;
                $timeout = $task->getTimeOut();

                go(function () use ($task) {
                    try {
                        App::$debug && Log::record("Tasker", "异步执行：" . print_r($task, true));
                        $task->onStart();
                    } catch (\Throwable $e) {
                        $task->onAbort($e);
                    }
                    App::$debug && Log::record("Tasker", "异步执行结束：");
                    $task->onStop();
                }, $timeout);
            }
        }
        Cache::init(0, Variables::getCachePath("tasker", DS))->set("tasker_list", $data);

    }


    /**
     * 计算下一次执行时间
     * @param $minute int 分钟
     * @param $hour int 时
     * @param $day int 天
     * @param $month int 月
     * @param $week int 周
     * @return float 返回下次执行时间
     */
    private static function getNext(int $minute, int $hour, int $day, int $month, int $week, bool $loop)
    {
        $days = intval(date('t', strtotime("+{$month} month")));//获取指定的月份天数
        $time = $minute * 60 + $hour * 60 * 60 + $day * 60 * 60 * 24 + $month * 60 * 60 * 24 * $days + $week * 60 * 60 * 24 * 7;
        //if($day!=0||$month!=0||)
        if ($loop == 0) {//如果是循环的话，每小时，每天，每周，每月
            $date = mktime(0, 0, 0, date('m'), 1, date('Y'));//取当前月的第一天

            $add = $month * 60 * 60 * 24 * $days;
            if ($month == 0) {
                $date = mktime(0, 0, 0, date("m"), date("d") - date("w") + 1, date("Y"));//取当前周的第一天
                $add = $week * 60 * 60 * 24 * 7;
                if ($week == 0) {
                    $date = mktime(0, 0, 0, date('m'), date('d'), date('Y'));//获取当天的
                    $add = $day * 60 * 60 * 24;
                    if ($day == 0) {
                        $date = mktime($hour, 0, 0, date('m'), date('d'), date('Y'));//获取当天的
                        $add = $hour * 60 * 60;
                    }
                }
            }
            //判断出循环类型
            $ret_time = $date + $time;
            if ($add <= 0) $add = 60;
            while ($ret_time < time()) {
                $ret_time = $ret_time + $add;
            }
        } else {
            $ret_time = time() + $time;
        }
        return $ret_time;
    }

}
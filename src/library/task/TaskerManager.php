<?php
/*******************************************************************************
 * Copyright (c) 2022. CleanPHP. All Rights Reserved.
 ******************************************************************************/

namespace library\task;

use cleanphp\App;
use cleanphp\base\Variables;
use cleanphp\cache\Cache;
use cleanphp\file\Log;
use library\task\Cron\CronExpression;

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
     * 判断是否存在指定的定时任务
     * @param $key
     * @return bool
     */
    public static function has($key): bool
    {
        $list = self::list();
        /**
         * @var $value TaskInfo
         */
        foreach ($list as $value) {
            if ($key === $value->key || $key === $value->name) {
                return true;
            }
        }
        return false;
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
        $new = [];
        foreach ($list as $value) {
            if ($key !== $value->key && $key !== $value->name) {
                $new[] = $value;
            }
        }
        Cache::init(0, Variables::getCachePath("tasker", DS))->set("tasker_list", $new);
    }


    /**
     * 添加一个定时任务，与linux定时任务语法完全一致
     * @param string $cron 定时任务时间包，使用{@link TaskerTime}来指定或手写cron字符串（不含秒数位，不支持问号）
     * @param TaskerAbstract $taskerAbstract 需要运行的定时任务，需要继承{@link TaskerAbstract}类并实现{@link TaskerAbstract::onStart()}方法
     * @param string $name 定时任务名称
     * @param int $times 定时任务的执行次数，当times=-1的时候为循环任务
     * 返回定时任务ID
     * @return string
     */
    public static function add(string $cron, TaskerAbstract $taskerAbstract, string $name, int $times = 1): string
    {

        if ($cron === "") {
            Log::record("Tasker", "该任务：$name 立即执行");
            //属于立即执行
            go(function () use ($taskerAbstract) {
                try {
                    $taskerAbstract->onStart();
                } catch (\Throwable $exception) {
                    $taskerAbstract->onAbort($exception);
                } finally {
                    $taskerAbstract->onStop();
                }

            }, $taskerAbstract->getTimeOut());
            return '';
        }

        $task = new TaskInfo();
        $task->name = $name;
        $task->cron = $cron;
        $task->times = $times;
        $task->loop = $times==-1;
        $task->key = uniqid("task_");

        $task->next = CronExpression::factory($cron)->getNextRunDate()->getTimestamp();
        $task->closure = $taskerAbstract;
        $list = self::list();
        $list[] = $task;
        Cache::init(0, Variables::getCachePath("tasker", DS))->set("tasker_list", $list);
        if (App::$debug) {
            Log::record("Tasker", "添加定时任务：$name => " . get_class($taskerAbstract));
            Log::record("Tasker", "初次添加后，执行时间为：" . date("Y-m-d H:i:s", $task->next));
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
            //次序=0
            if ($value->times === 0) {
                App::$debug && Log::record("Tasker", "该ID ({$value->name})[{$value->key}] 的定时任务执行完毕");
                unset($data[$k]);
            } elseif ($value->next <= time()) {
                $time = CronExpression::factory($value->cron)->getNextRunDate()->getTimestamp();
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
                    } finally {
                        App::$debug && Log::record("Tasker", "异步执行结束：");
                        $task->onStop();
                    }

                }, $timeout);
            }
        }
        Cache::init(0, Variables::getCachePath("tasker", DS))->set("tasker_list", $data);

    }



}
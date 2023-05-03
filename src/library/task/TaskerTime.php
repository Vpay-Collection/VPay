<?php
/*******************************************************************************
 * Copyright (c) 2022. CleanPHP. All Rights Reserved.
 ******************************************************************************/

/**
 * Package: extend\ankioTask\core
 * Class cycle
 * Created By ankio.
 * Date : 2022/5/10
 * Time : 19:29
 * Description :
 */

namespace library\task;

class TaskerTime
{

    /**
     * 每天【{@link $hour}时{@link $mintue}分】执行任务
     * @param $hour int 小时
     * @param $minute int 分钟
     * @return array
     */
    static public function day(int $hour, int $minute): array
    {
        return [$minute, $hour, 1, 0, 0];
    }

    /**
     * 每隔【{@link $day}天的{@link $hour}时{@link $mintue}分】执行任务
     * @param $day int 天数
     * @param $hour int 时间
     * @param $minute int 分钟
     * @return array
     */
    static public function nDay(int $day, int $hour, int $minute): array
    {
        return [$minute, $hour, $day, 0, 0];
    }

    /**
     * 每天每隔【{@link $hour}时的第{@link $mintue}分钟】执行任务
     * @param int $hour 小时
     * @param $minute int 分钟
     * @return array
     */
    static public function nHour(int $hour, int $minute): array
    {
        return [$minute, 1, $hour, 0, 0];
    }

    /**
     * 每小时的【第{@link $mintue}分钟】执行任务
     * @param $minute int 分钟
     * @return array
     */
    static public function hour(int $minute): array
    {
        return [$minute, 1, 0, 0, 0];
    }

    /**
     * 【每隔{@link $mintue}分钟】执行任务
     * @param $minute int 分钟
     * @return array
     */
    static public function nMinute(int $minute): array
    {
        return [$minute, 0, 0, 0, 0];
    }

    /**
     * 每隔{@link $week}周的，{@link $hour}时{@link $mintue}分钟执行任务，具体是哪一天执行，取决于定时任务创建的当天。
     * 例如 11月1日周二创建该定时任务，那么一周后就是11月8日执行该定时任务。
     * @param $week int 周数
     * @param $hour int 小时
     * @param $minute int 分钟
     * @return array
     */
    static public function week(int $week, int $hour, int $minute): array
    {
        return [$minute, $hour, 0, 0, $week];
    }

    /**
     * 每个月的第{@link $day}天，{@link $hour}时{@link $mintue}分钟执行任务。
     * @param $day int 天
     * @param $hour int 小时
     * @param $minute int 分钟
     * @return array
     */
    static public function month(int $day, int $hour, int $minute): array
    {
        return [$minute, $hour, $day, 1, 0];
    }

}
<?php
/*
 * Copyright (c) 2023. Ankio.  由CleanPHP4强力驱动。
 */

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
     * @return string
     */
    static public function day(int $hour, int $minute): string
    {
        return "$minute $hour * * *";
    }

    /**
     * 每隔【{@link $day}天的{@link $hour}时{@link $mintue}分】执行任务
     * @param $day int 天数
     * @param $hour int 时间
     * @param $minute int 分钟
     * @return string
     */
    static public function nDay(int $day, int $hour, int $minute): string
    {
        return "$minute $hour */$day * *";
    }

    /**
     * 每天每隔【{@link $hour}时的第{@link $mintue}分钟】执行任务
     * @param int $hour 小时
     * @param $minute int 分钟
     * @return string
     */
    static public function nHour(int $hour, int $minute): string
    {
        return "$minute */$hour * * *";
    }

    /**
     * 每小时的【第{@link $mintue}分钟】执行任务
     * @param $minute int 分钟
     * @return string
     */
    static public function hour(int $minute): string
    {
        return "$minute */1 * * *";
    }

    /**
     * 【每隔{@link $mintue}分钟】执行任务
     * @param $minute int 分钟
     * @return string
     */
    static public function nMinute(int $minute): string
    {
        if ($minute === 0) return "";
        return "*/$minute * * * *";
    }



}
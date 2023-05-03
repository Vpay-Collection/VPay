<?php
/*******************************************************************************
 * Copyright (c) 2022. Ankio. All Rights Reserved.
 ******************************************************************************/

/**
 * Package: library\task
 * Class TaskInfo
 * Created By ankio.
 * Date : 2022/11/19
 * Time : 17:11
 * Description :
 */

namespace library\task;

class TaskInfo
{
    public string $key = "";//任务ID
    public string $name = "";//任务名称
    public int $minute = 0;
    public int $hour = 0;
    public int $day = 0;
    public int $week = 0;
    public int $month = 0;
    public int $times = 0;
    public int $next = 0;//下次的执行时间
    public bool $loop = false;//是否循环
    public ?TaskerAbstract $closure = null;//序列化的执行事件
}
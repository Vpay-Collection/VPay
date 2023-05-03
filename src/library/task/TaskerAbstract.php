<?php
/*******************************************************************************
 * Copyright (c) 2022. CleanPHP. All Rights Reserved.
 ******************************************************************************/

/**
 * Package: extend\ankioTask\core
 * Class ATasker
 * Created By ankio.
 * Date : 2022/6/4
 * Time : 09:35
 * Description :
 */

namespace library\task;


use Throwable;

abstract class TaskerAbstract
{

    /**
     * 该任务最长的运行时间
     * @return int
     */
    abstract public function getTimeOut(): int;

    /**
     * 任务被启动的时候
     * @return mixed
     */
    abstract public function onStart();

    /**
     * 任务停止的时候
     * @return mixed
     */
    abstract public function onStop();

    /**
     * 任务因为异常退出的时候
     * @param Throwable $e
     * @return mixed
     */
    abstract public function onAbort(Throwable $e);


}
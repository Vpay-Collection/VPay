<?php
/*******************************************************************************
 * Copyright (c) 2022. CleanPHP. All Rights Reserved.
 ******************************************************************************/

namespace app\core\event;


/**
 * Interface EventListener
 * @package app\core\event
 */
interface EventListener
{

    /**
     * 事件接收器
     * @param $event string 事件名
     * @param $msg string|array 自行判断类型
     * @return mixed
     */
	public function handleEvent(string $event, $msg);
}

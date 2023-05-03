<?php
/*
 * Copyright (c) 2023. Ankio. All Rights Reserved.
 */

/**
 * Package: cleanphp\base
 * Class MainApp
 * Created By ankio.
 * Date : 2022/11/9
 * Time : 15:59
 * Description :
 */

namespace cleanphp\base;

interface  MainApp
{
    /**
     * 框架刚启动的时候
     * 可以在这里注册 frame_init事件，部分拓展的运行位置也在此时开始，包括定时任务、Websocket等
     * @return mixed
     */
    function onFrameworkStart();

    /**
     * 请求到达时
     * @return mixed
     */
    function onRequestArrive();

    /**
     * 请求结束时
     * @return mixed
     */
    function onRequestEnd();

}
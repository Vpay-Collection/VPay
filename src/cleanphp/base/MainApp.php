<?php
/*
 * Copyright (c) 2023. Ankio.  由CleanPHP4强力驱动。
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
     * @return void
     */
    function onFrameworkStart(): void;

    /**
     * 请求到达时
     * @return void
     */
    function onRequestArrive(): void;

    /**
     * 请求结束时
     * @return void
     */
    function onRequestEnd(): void;

}
<?php
/*
 * Copyright (c) 2023. Ankio. All Rights Reserved.
 */

/**
 * Package: cleanphp\engine
 * Class ResponseEngine
 * Created By ankio.
 * Date : 2022/11/11
 * Time : 17:30
 * Description :
 */

namespace cleanphp\engine;

use cleanphp\base\Response;

abstract class BaseEngine
{

    /**
     * 渲染的输出类型
     * @return string
     */
    abstract function getContentType(): string;

    /**
     * 渲染数据
     * @param $data
     * @return string
     */
    abstract function render(...$data): string;

    /**
     * 错误渲染
     * @param string $msg 错误信息
     * @param array $traces 堆栈
     * @param string $dumps 错误发生之前的输出信息
     */
    abstract function renderError(string $msg, array $traces, string $dumps, string $tag);

    /**
     */
    function renderMsg(bool $err = false, int $code = 404, string $title = "", $msg = "", int $time = -1, string $url = '/', string $desc = "立即跳转"): string
    {
        if ($time == 0) {
            Response::location($url);
        }
        return "";
    }

    /**
     * 控制器渲染自定义错误
     * @param $controller
     * @param $method
     * @return string|null
     */
    function onControllerError($controller, $method): ?string
    {
        return null;
    }

    abstract function onNotFound($msg = "");

}
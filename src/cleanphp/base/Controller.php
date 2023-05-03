<?php
/*
 * Copyright (c) 2023. Ankio. All Rights Reserved.
 */

namespace cleanphp\base;


use cleanphp\engine\EngineManager;

class Controller
{
    protected int $code = 200;

    public function __construct()
    {
        $result = $this->__init();
        if (!empty($result)) (new Response())->render($result)->code($this->code)->contentType(EngineManager::getEngine()->getContentType())->send();
        EventManager::trigger("__on_controller_create__", $this);
    }


    /**
     * 初始化函数
     */
    public function __init()
    {
        return null;
    }

    /**
     * 数据渲染
     * @param ...$data
     */
    public function render(...$data): string
    {
        return EngineManager::getEngine()->render(...$data);
    }

    public function getCode(): int
    {
        return $this->code;
    }


}

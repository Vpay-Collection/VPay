<?php
/*
 * Copyright (c) 2023. Ankio. All Rights Reserved.
 */

namespace cleanphp\base;


use cleanphp\engine\EngineManager;

class Controller
{
    public function __construct()
    {
        $result = $this->__init();
        if (!empty($result)){
            $engine = EngineManager::getEngine();
            (new Response())
                ->render($result)
                ->contentType($engine->getContentType())
                ->setHeaders($engine->getHeaders())
                ->code($engine->getCode())
                ->send();
        }
        EventManager::trigger("__on_controller_create__", $this);
    }


    /**
     * 初始化函数
     * 返回string就需要提前输出
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



}

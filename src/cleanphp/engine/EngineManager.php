<?php
/*
 * Copyright (c) 2023. Ankio.  由CleanPHP4强力驱动。
 */

/**
 * Package: cleanphp\engine
 * Class EngineManager
 * Created By ankio.
 * Date : 2023/4/23
 * Time : 20:30
 * Description :
 */

namespace cleanphp\engine;

class EngineManager
{
    protected static $engine = null;


    /**
     * 获取渲染引擎
     * @return JsonEngine|BaseEngine|ViewEngine|null
     */
    public static function getEngine()
    {
        //如果之前没有设置输出引擎，则启用文档引擎
        if(empty(self::$engine)){
            self::setDefaultEngine(new ViewEngine());
        }
        return self::$engine;
    }

    /**
     * 设置默认引擎
     * @param $engine BaseEngine
     */
    static function setDefaultEngine(BaseEngine $engine): void
    {
        self::$engine = $engine;
    }


}
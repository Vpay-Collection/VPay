<?php
/*
 * Copyright (c) 2023. Ankio. All Rights Reserved.
 */

/**
 * Package: cleanphp\base
 * Class Loader
 * Created By ankio.
 * Date : 2022/11/9
 * Time : 23:07
 * Description :
 */

namespace cleanphp\base;

use cleanphp\App;
use cleanphp\file\Log;

class Loader
{

    /**
     * 注册自动加载
     */
    public static function register()
    {
        spl_autoload_register(__NAMESPACE__ . "\Loader::autoload", true, true);
        //注册第三方库的自动加载
        if (is_dir(Variables::getLibPath())) {

            $data = scandir(Variables::getLibPath());
            foreach ($data as $value) {
                if (substr($value, 0, 1) !== ".") {
                    $file = Variables::setPath(Variables::getLibPath(), $value, 'autoload.php');
                    if (file_exists($file)) {
                        include_once $file;
                    }
                }

            }

        }
        $file = Variables::setPath(APP_DIR, 'vendor', 'autoload.php');

        if (file_exists($file)) include_once $file;
    }

    /**
     * 框架本身的自动加载
     * @param string $raw
     */
    public static function autoload(string $raw)
    {
        $real_class = str_replace("\\", DS, $raw) . ".php";
        //拼接类名文件
        $file = APP_DIR . DS . $real_class;
        //存在就加载
        if (file_exists($file)) {
            include_once $file;
            //细节不重要
            if (App::$debug && strpos($file, "cleanphp/") === false)
                Log::record("Loader", $raw);
        }
    }


}
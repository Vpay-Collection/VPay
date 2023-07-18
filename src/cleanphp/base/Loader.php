<?php
/*
 * Copyright (c) 2023. Ankio.  由CleanPHP4强力驱动。
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
     * 已加载的文件数组
     *
     * @var array
     */
    private static array $loadedFiles = [];

    /**
     * 注册自动加载
     */
    public static function register(): void
    {
        spl_autoload_register(__NAMESPACE__ . "\Loader::autoload", true, true);
        // 注册第三方库的自动加载
        $libPath = Variables::getLibPath();
        if (is_dir($libPath)) {
            $data = scandir($libPath);
            foreach ($data as $value) {
                if (!str_starts_with($value, ".")) {
                    $file = Variables::setPath($libPath, $value, 'autoload.php');
                    self::includeFile($file);
                }
            }
        }
        self::includeFile(Variables::setPath(APP_DIR, 'vendor', 'autoload.php'));
    }

    /**
     * 框架本身的自动加载
     *
     * @param string $raw
     */
    public static function autoload(string $raw): void
    {
        $realClass = str_replace("\\", DS, $raw) . ".php";
        // 拼接类名文件
        $file = APP_DIR . DS . $realClass;
        // 存在就加载
        self::includeFile($file);
    }

    /**
     * 引入文件并记录日志
     *
     * @param string $file
     */
    private static function includeFile(string $file): void
    {
        if (isset(self::$loadedFiles[$file])) {
            return; // 文件已加载
        }

        if (file_exists($file)) {
            include $file;
            self::$loadedFiles[$file] = true; // 将文件标记为已加载
        }
    }
}

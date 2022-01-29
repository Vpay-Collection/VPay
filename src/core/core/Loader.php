<?php
/*******************************************************************************
 * Copyright (c) 2022. CleanPHP. All Rights Reserved.
 ******************************************************************************/

namespace app\core\core;

use app\core\error\Error;
use app\core\debug\Log;


/**
 * Class Loader
 * @package app\core\core
 * Date: 2020/11/19 11:47 下午
 * Author: ankio
 * Description:自动加载类
 */
class Loader
{
	/**
     * 注册自动加载
     */
	public static function register()
    {
        if(isDebug()) $GLOBALS["frame"]["files"][]="注册自动加载";
        spl_autoload_register('app\\core\\core\\Loader::autoload', true, true);
        //注册第三方库的自动加载
        $data = scandir(APP_LIB);
        foreach ($data as $value) {
            if ($value != '.' && $value != '..' && $value!=".DS_Store") {
                $file = APP_LIB .  $value . DS . 'autoload.php';
                if (file_exists($file)){
                    require_once $file;
                }
            }
        }
        require_once APP_COMPOSER . 'autoload.php';
    }
	/**
     * 框架本身的自动加载
     * @param string $realClass 自动加载的类名
     */
	public static function autoload(string $realClass)
    {
		//解析出路径与类名
        $classArr = self::getClass($realClass);
        $class = $classArr['class'] . '.php';
        $namespace = $classArr['namespace'];
		//拼接类名文件
        $file = APP_DIR . DS . str_replace('app/', '', $namespace) . DS . $class;
        //存在就加载
        if (file_exists($file)) {
            require_once $file;
            if(isDebug()) $GLOBALS["frame"]["files"][]="加载：".$namespace.DS.$class;
        }
    }

	/**
     * 根据命名空间解析类名与路径
     * @param string $class
     * @return array
     */
	public static function getClass(string $class): array
    {
        if (strpos($class, '.')) Error::err('[Loader]"' . $class . '" 不是一个有效的类名！');
        $name = explode('\\', $class);
        $size = sizeof($name);
        $namespace = '';
        for ($i = 0; $i < $size - 1; $i++) {
            $namespace .= $name[$i] . (($i < $size - 2) ? '/' : '');
        }
        return ['namespace' => $namespace, 'class' => $name[$size - 1]];
    }

}

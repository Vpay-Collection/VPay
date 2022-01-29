<?php
/*******************************************************************************
 * Copyright (c) 2022. CleanPHP. All Rights Reserved.
 ******************************************************************************/
/**
 * Class Debug
 * Created By ankio.
 * Date : 2022/1/14
 * Time : 3:30 下午
 * Description :
 */

namespace app\core\debug;

use app\core\config\Config;

class Debug
{
    private static ?Debug $instance = null;
    private static int $validTime = 1;
    private $handler;//实例
    /**
     * 输出调试信息
     * @param $tag
     * @param $msg
     */
    public static function i($tag, $msg)
    {
        $GLOBALS["frame"][$tag] = $msg;
       // $self = self::getInstance($tag);
       // $self->write($msg);
    }

    /**
     * 获取实例
     * @param $tag
     * @return Debug
     */
    public static function getInstance($tag): Debug
    {
        if (self::$instance == null) {
            self::$instance = new Debug();
        }
        $file = APP_LOG . date('Y-m-d') . DS . 'debug_'.$tag.'.log';
        $dir_name = dirname($file);
        //目录不存在就创建
        if (!file_exists($dir_name)) {
            Log::mkdirs($dir_name);
        }
        self::$instance->handler = fopen($file, 'a');
        return self::$instance;
    }
    /**
     * 写入日志文件
     * @param $msg
     */
    protected function write($msg)
    {
        $msg = '[' . date('Y-m-d H:i:s') . ']' . $msg
            . "\n";
        flock($this->handler, LOCK_EX);
        fwrite($this->handler, $msg, strlen($msg));
        flock($this->handler, LOCK_UN);
        fclose($this->handler);
        //删除指定日期之前的日志
        Log::rm(date('Y-m-d', strtotime("- " . self::$validTime . " day")));
    }




}
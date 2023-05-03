<?php
/*
 * Copyright (c) 2023. Ankio. All Rights Reserved.
 */

/**
 * Package: cleanphp\base
 * Class Log
 * Created By ankio.
 * Date : 2022/11/9
 * Time : 22:12
 * Description :
 */

namespace cleanphp\file;

use cleanphp\base\Config;
use cleanphp\base\Variables;


class Log
{
    const TYPE_ERROR = 0;
    const TYPE_INFO = 1;
    const TYPE_WARNING = 2;
    private static ?Log $instance = null;
    private static int $validate = 30;
    private string $temp = "";
    private string $file = "";
    private string $tag = "";
    private int $type = 1;//写入的数据类型

    public function __construct($temp)
    {
        $this->temp = Variables::getLogPath($temp . '.log');
    }

    /**
     * 输出信息
     * @param $tag
     * @param $msg
     * @param int $type
     */
    public static function record($tag, $msg, int $type = self::TYPE_INFO)
    {
        self::getInstance($tag)->addTemp(self::getInstance($tag)->setType($type)->write($msg));
    }

    /**
     * 输出信息
     * @param $tag
     * @param $msg
     * @param int $type
     * @param string $pre
     */
    public static function recordAsLine($tag, $msg, int $type = self::TYPE_INFO, string $pre = "")
    {
        foreach (explode("\n", $msg) as $item) {
            self::getInstance($tag)->addTemp(self::getInstance($tag)->setType($type)->write($pre . trim($item)));
        }

    }

    private function addTemp($msg)
    {
        $handler = fopen($this->temp, 'a');
        fwrite($handler, $msg, strlen($msg));
        fclose($handler);
    }

    /**
     * 获取实例
     * @param $tag
     * @param string $filename
     * @return Log
     */
    public static function getInstance($tag, string $filename = "cleanphp"): Log
    {
        if (self::$instance == null) {
            self::$instance = new Log(uniqid());
        }
        self::$instance->tag = $tag;
        self::$instance->file = Variables::getLogPath(date('Y-m-d'), Variables::get("__frame_log_tag__", "") . $filename . '.log');
        $dir_name = dirname(self::$instance->file);
        if (!file_exists($dir_name)) {
            File::mkDir($dir_name);
        }
        self::$validate = Config::getConfig("frame")["log"] ?? 30;
        return self::$instance;
    }

    /**
     * 写入日志文件
     * @param $msg
     * @return string
     */
    protected function write($msg): string
    {
        $m_timestamp = sprintf("%.3f", microtime(true)); // 带毫秒的时间戳
        $timestamp = floor($m_timestamp); // validate
        $milliseconds = str_pad(strval(round(($m_timestamp - $timestamp) * 1000)), 3, "0"); // 毫秒
        $type = $this->type === Log::TYPE_INFO ? "INFO" : ($this->type === Log::TYPE_ERROR ? "ERROR" : "WARNING");
        return '[ ' . date('Y-m-d H:i:s', $timestamp) . '.' . $milliseconds . ' ] [ ' . $type . ' ] [ ' . $this->tag . ' ] ' . $msg . "\n";
    }

    /**
     * 设置写入的数据类型
     * @param int $type
     * @return Log
     */
    private function setType(int $type): Log
    {
        $this->type = $type;
        return $this;
    }

    public function getTempLog(): array
    {
        $file_handle = fopen($this->temp, "r");
        $ret = [];
        $max = 500;//最多500行
        if ($file_handle) {
            //接着采用 while 循环一行行地读取文件，然后输出每行的文字
            while (!feof($file_handle) && $max > 0) { //判断是否到最后一行
                $line = fgets($file_handle, 4096); //读取一行文本
                $ret[] = $line;
                $max--;
            }
        }
        fclose($file_handle);//关闭文件
        return $ret;
    }

    /**
     * 当日志变量被销毁后，统一写入文件
     */
    public function __destruct()
    {
        $id = Variables::get("__async_task_id__", "");
        $start = "-----------[session $id start]-----------\n";
        $end = "-----------[session $id end]-----------\n\n";
        $handler = fopen(self::$instance->file, 'a');
        if (flock($handler, LOCK_EX)) {
            fwrite($handler, $start, strlen($start));
            $file_handle = fopen($this->temp, "r");
            if ($file_handle) {
                //接着采用 while 循环一行行地读取文件，然后输出每行的文字
                while (!feof($file_handle)) { //判断是否到最后一行
                    $line = fgets($file_handle, 4096); //读取一行文本
                    fwrite($handler, $line, strlen($line));
                }
            }
            fclose($file_handle);//关闭文件
            unlink($this->temp);
            fwrite($handler, $end, strlen($end));
            flock($handler, LOCK_UN);
        }
        fclose($handler);
        //删除指定日期之前的日志
        $this->rm(date('Y-m-d', strtotime("- " . self::$validate . " day")));
    }

    /**
     * 删除日志
     * @param $date
     */
    private function rm($date = null)
    {
        if (is_dir(Variables::getLogPath($date))) {
            File::del(Variables::getLogPath($date));
        }
    }

}
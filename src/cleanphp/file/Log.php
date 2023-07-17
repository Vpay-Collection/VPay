<?php
/*
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
    private int $type = 1;

    public function __construct($temp)
    {
        $this->temp = Variables::getLogPath($temp . '.log');
        File::mkDir(Variables::getLogPath());
    }

    public static function record($tag, $msg, int $type = self::TYPE_INFO): void
    {
        self::getInstance($tag)->addTemp(self::getInstance($tag)->setType($type)->write($msg));
    }

    public static function recordAsLine($tag, $msg, int $type = self::TYPE_INFO, string $pre = ""): void
    {
        foreach (explode("\n", $msg) as $item) {
            self::getInstance($tag)->addTemp(self::getInstance($tag)->setType($type)->write($pre . trim($item)));
        }
    }

    private function addTemp($msg): void
    {
        $handler = fopen($this->temp, 'a');
        fwrite($handler, $msg);
        fclose($handler);
    }

    public static function getInstance($tag, string $filename = "cleanphp"): Log
    {
        if (self::$instance == null) {
            self::$instance = new Log(uniqid());
        }
        self::$instance->tag = $tag;
        self::$instance->file = Variables::getLogPath(date('Y-m-d'), Variables::get("__frame_log_tag__", "") . $filename . '.log');
        File::mkDir(dirname(self::$instance->file));
        self::$validate = Config::getConfig("frame")["log"] ?? 30;
        return self::$instance;
    }

    protected function write($msg): string
    {
        $m_timestamp = sprintf("%.3f", microtime(true));
        $timestamp = floor($m_timestamp);
        $milliseconds = str_pad(strval(round(($m_timestamp - $timestamp) * 1000)), 3, "0");
        $type = $this->type === Log::TYPE_INFO ? "INFO" : ($this->type === Log::TYPE_ERROR ? "ERROR" : "WARNING");
        return '[ ' . date('Y-m-d H:i:s', $timestamp) . '.' . $milliseconds . ' ] [ ' . $type . ' ] [ ' . $this->tag . ' ] ' . $msg . "\n";
    }

    private function setType(int $type): Log
    {
        $this->type = $type;
        return $this;
    }
    public function getTempLog(): array
    {
        $lines = [];
        $lineCount = 0;
        $fileHandle = fopen($this->temp, 'r');

        if ($fileHandle) {
            while (($line = fgets($fileHandle)) !== false && $lineCount < 500) {
                $lines[] = $line;
                $lineCount++;
            }

            fclose($fileHandle);
        }

        return $lines;
    }


    public function __destruct()
    {
        $id = Variables::get("__async_task_id__", "");
        $start = "-----------[session $id start]-----------\n";
        $end = "-----------[session $id end]-----------\n\n";
        $handler = fopen(self::$instance->file, 'a');
        if (flock($handler, LOCK_EX)) {
            fwrite($handler, $start);
            $lines = file($this->temp, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            fwrite($handler, implode("\n", $lines));
            unlink($this->temp);
            fwrite($handler, $end);
            flock($handler, LOCK_UN);
        }
        fclose($handler);
        $this->rm(date('Y-m-d', strtotime("- " . self::$validate . " day")));
    }

    private function rm($date = null)
    {
        File::del(Variables::getLogPath($date));
    }
}

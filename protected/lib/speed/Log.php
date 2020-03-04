<?php

namespace lib\speed;
class Log
{
    private $handler = null;
    private $level = 15;


    public function __construct($file = '', $level = 15)
    {
        $this->handler = fopen($file, 'a');
        $this->level = $level;
    }

    public function __destruct()
    {
        fclose($this->handler);
    }

    public function DEBUG($msg)
    {
        $this->write(1, $msg);
    }

    /**
     * @param $level
     * @param $msg
     */
    protected function write($level, $msg)
    {
        $msg = '[' . date('Y-m-d H:i:s') . '][' . $this->getLevelStr($level) . '] ' . $msg . "\n";
        fwrite($this->handler, $msg, 4096);
    }

    private function getLevelStr($level)
    {
        switch ($level) {
            case 1:
                return 'debug';
                break;
            case 2:
                return 'info';
                break;
            case 4:
                return 'warn';
                break;
            case 8:
                return 'error';
                break;
            default:
                return 'debug';
        }
    }

    public function WARN($msg)
    {
        $this->write(4, $msg);
    }

    public function ERROR($msg)
    {
        $debugInfo = debug_backtrace();
        $stack = "[";
        foreach ($debugInfo as $key => $val) {
            if (array_key_exists("file", $val)) {
                $stack .= ",file:" . $val["file"];
            }
            if (array_key_exists("line", $val)) {
                $stack .= ",line:" . $val["line"];
            }
            if (array_key_exists("function", $val)) {
                $stack .= ",function:" . $val["function"];
            }
        }
        $stack .= "]";
        $this->write(8, $stack . $msg);
    }

    public function INFO($msg)
    {
        $this->write(2, $msg);
    }
}

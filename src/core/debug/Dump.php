<?php
/*******************************************************************************
 * Copyright (c) 2022. CleanPHP. All Rights Reserved.
 ******************************************************************************/

namespace app\core\debug;

use app\core\utils\StringUtil;
use ReflectionClass;
use ReflectionException;

/**
 * Class Dump
 * @package app\core\debug
 * Date: 2020/11/20 11:24 下午
 * Author: ankio
 * Description: 调试输出类
 */
class Dump
{

    /**
     * 输出对象
     * @param       $param
     * @param int $i
     */
	private function dumpObj($param, int $i = 0)
    {
        $className = get_class($param);
        if ($className == 'stdClass' && $result = json_encode($param)) {
            $this->dumpArr(json_decode($result, true), $i);
            return;
        }
        static $objId = 1;
        echo "<b style='color: #333;'>Object</b> <i style='color: #333;'>$className</i>";
        $objId++;
        $this->dumpProp($param, $className, $objId);

    }

    /**
     * 输出数组
     * @param       $param
     * @param int $i
     */
	private function dumpArr($param, int $i = 0)
    {

        $len = count($param);
        $space = str_repeat("    ", $i);
        $i++;
        echo "<b style='color: #333;'>array</b> <i style='color: #333;'>(size=$len)</i> \r\n";
        if ($len === 0)
            echo $space . "  <i  style='color: #888a85;'>empty</i> \r\n";
        foreach ($param as $key => $val) {
            $str = htmlspecialchars(StringUtil::get($key)->chkCode(), strlen($key));
            echo $space . sprintf("<i style='color: #333;'> %s </i><i  style='color: #888a85;'>=&gt;", $str);
            $this->dumpType($val, $i);
            echo "</i> \r\n";
        }
    }

    /**
     * 自动选择类型输出
     * @param       $param
     * @param int $i
     */
	public function dumpType($param, int $i = 0)
    {

        switch (gettype($param)) {
            case 'NULL' :
                echo '<span style="color: #3465a4">null</span>';
                break;
            case 'boolean' :
                echo '<small style="color: #333;font-weight: bold">boolean</small> <span style="color:#75507b">' . ($param ? 'true' : 'false') . "</span>";
                break;
            case 'integer' :
                echo "<small style='color: #333;font-weight: bold'>int</small> <i style='color:#4e9a06'>$param</i>";
                break;
            case 'double' :
                echo "<small style='color: #333;font-weight: bold'>float</small> <i style='color:#f57900'>$param</i>";
                break;
            case 'string' :
                $this->dumpString($param);
                break;
            case 'array' :
                $this->dumpArr($param, $i);
                break;
            case 'object' :
                $this->dumpObj($param, $i);
                break;
            case 'resource' :
                echo '<i style=\'color:#3465a4\'>resource</i>';
                break;
            default :
                echo '<i style=\'color:#3465a4\'>unknown type</i>';
                break;
        }
    }

    /**
     * 输出文本
     * @param $param
     */
	private function dumpString($param)
    {

        $str = sprintf("<small style='color: #333;font-weight: bold'>string</small> <i style='color:#cc0000'>'%s'</i> <i>(length=%d)</i>", htmlspecialchars(StringUtil::get($param)->chkCode()), strlen($param));
        echo $str;
    }

    /**
     * 输出类对象
     * @param $obj
     * @param $className
     * @param $num
     */
	public function dumpProp($obj, $className, $num)
    {
        if ($className == get_class($obj) && $num > 2) return;
        static $pads = [];
        try {
            $reflect = new ReflectionClass($obj);
        } catch (ReflectionException $e) {
            $reflect = null;
        }
        if (!$reflect) {
            echo "Something Err";
            return;
        }
        $prop = $reflect->getProperties();

        $len = count($prop);
        echo "<i style='color: #333;'> (size=$len)</i>";
        array_push($pads, "    ");
        for ($i = 0; $i < $len; $i++) {
            $index = $i;
            $prop[$index]->setAccessible(true);
            $prop_name = $prop[$index]->getName();
            echo "\n", implode('', $pads), sprintf("<i style='color: #333;'> %s </i><i style='color:#888a85'>=&gt;", $prop_name);
            $this->dumpType($prop[$index]->getValue($obj), $num);
        }
        array_pop($pads);
    }

}

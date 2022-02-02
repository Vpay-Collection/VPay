<?php
/*******************************************************************************
 * Copyright (c) 2022. CleanPHP. All Rights Reserved.
 ******************************************************************************/

namespace app\core\utils;

/**
 * 字符串工具类
 */
class StringUtil{

    private static ?StringUtil $stringUtil=null;
    private string $str="";

    /**
     * @param string $s 需要处理的字符串
     * @return StringUtil
     */
    public static function get(string $s=""): StringUtil
    {
        if(self::$stringUtil==null){
            self::$stringUtil=new StringUtil();
        }
        self::$stringUtil->setStr($s);
        return self::$stringUtil;
    }

    /**
     * @param string $str 设置当前操作的字符串
     */
    public function setStr(string $str){
        $this->str=$str;
    }

    /**
     * @param string $s 被对比的字符串
     * @return bool
     */
    public function equals(string $s): bool
    {
        return $this->str===$s;
    }
    /**
     * @param string $s 被对比的字符串
     * @return bool
     */
    public function contains(string $s): bool
    {
        return strpos($this->str,$s)!== false;
    }

    /**
     * @param string $subString 以$subString开头
     * @return bool
     */
    public function startsWith(string $subString): bool
    {
        return substr($this->str, 0, strlen($subString)) === $subString;
        // 或者 strpos($s2, $s1) === 0
    }

    /**
     * @param string $subString 以$subString结尾
     * @return bool
     */
    public function endsWith(string $subString): bool
    {
        return substr($this->str, strpos($this->str, $subString)) === $subString;
    }

    /**
     * @param string $endString 倒找文本，并截取掉
     * @return false|string|string[]
     */
    public function findEnd(string $endString){
        return str_replace($endString,"",substr($this->str,strrpos($this->str,'/')));
    }

    /**
     * @param string $startString 正找文本，并截取掉
     * @return false|string
     */
    public function findStart(string $startString){
        $str = substr($this->str,0,strpos($this->str,$startString));
        if($str=="")
            $str=$this->str;
        return $str;
    }
    /**
     *  获取随机字符串
     * @param int $length  字符串长度
     * @param bool $upper   是否包含大写字母
     * @param bool $lower   是否包含小写字母
     * @param bool $number  是否包含数字
     * @return string
     */
    public function getRandom(int $length = 8, bool $upper = true, bool $lower = true, bool $number = true): string
    {
        $charsList = [
            'abcdefghijklmnopqrstuvwxyz',
            'ABCDEFGHIJKLMNOPQRSTUVWXYZ',
            '0123456789',
        ];
        $chars     = "";
        if ($upper) {
            $chars .= $charsList[0];
        }
        if ($lower) {
            $chars .= $charsList[1];
        }
        if ($number) {
            $chars .= $charsList[2];
        }
        if ($chars === "") {
            $chars = $charsList[2];
        }
        $password = '';
        for ($i = 0; $i < $length; $i++) {
            $password .= $chars[mt_rand(0, strlen($chars) - 1)];
        }

        return $password;
    }

    /**
     * 检查编码并转换成UTF-8
     * @return string
     */
    public function chkCode(): string
    {
        $encode = mb_detect_encoding($this->str, array("ASCII", 'UTF-8', "GB2312", "GBK", 'BIG5'));
        return mb_convert_encoding($this->str, 'UTF-8', $encode);
    }


    /**
     * 中文截取无乱码
     * @param $start
     * @param $length
     * @return string|null
     */

    function sub($start, $length)

    {
       return mb_substr($this->str,$start,$length);
    }

}
<?php
/*
 * Copyright (c) 2023. Ankio. All Rights Reserved.
 */

/**
 * Package: cleanphp\objects
 * Class StringObject
 * Created By ankio.
 * Date : 2022/11/10
 * Time : 12:43
 * Description :
 */

namespace cleanphp\objects;

class StringBuilder
{

    private $str = "";

    public function __construct($str = "")
    {
        $this->str = $str;
    }

    /**
     * 添加函数
     * @param string $s
     * @return $this
     */
    public function append(string $s): StringBuilder
    {
        $this->str .= $s;
        return $this;
    }

    /**
     * @param string $sub_string 以$sub_string开头
     * @return bool
     */
    public function startsWith(string $sub_string): bool
    {
        return strpos($this->str, $sub_string) === 0;
    }

    /**
     * @param string $sub_string 以$sub_string结尾
     * @return bool
     */
    public function endsWith(string $sub_string): bool
    {
        return substr($this->str, strrpos($this->str, $sub_string)) === $sub_string;
    }

    /**
     * @param string $s 被对比的字符串
     * @return bool
     */
    public function equals(string $s): bool
    {
        return $this->str === $s;
    }

    /**
     * @param string $s 被对比的字符串
     * @return bool
     */
    public function contains(string $s): bool
    {
        return strpos($this->str, $s) !== false;
    }

    /**
     * 正找文本，并从找到的位置向后截取
     * @param string $startString
     * @return false|string
     */
    public function findAndSubStart(string $startString)
    {
        $pos = strpos($this->str, $startString);
        if ($pos == false) return "";
        return substr($this->str, $pos);
    }

    /**
     * 倒找文本，并从找到的位置向前截取
     * @param string $endString 倒找文本，并截取掉
     * @return false|string
     */
    public function findAndSubEnd(string $endString)
    {
        $pos = strpos($this->str, $endString);
        if (!$pos) return "";
        return substr($this->str, 0, $pos);
    }

    /**
     * 转换为文本
     * @return mixed|string
     */
    public function toString()
    {
        return $this->str;
    }

    /**
     * 编码转换
     * @param string $encode_code 编码类型
     * @return StringBuilder
     */
    public function convert(string $encode_code = "UTF-8"): StringBuilder
    {
        $encode = mb_detect_encoding($this->str, mb_detect_order());
        if ($encode !== $encode_code)
            $this->str = mb_convert_encoding($this->str, $encode_code, $encode);
        return $this;
    }
}
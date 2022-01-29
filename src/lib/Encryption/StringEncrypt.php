<?php
/*******************************************************************************
 * Copyright (c) 2021. CleanPHP. All Rights Reserved.
 ******************************************************************************/

namespace app\lib\Encryption;

/**
 * Class StringEncrypt
 * Created By ankio.
 * Date : 2022/1/14
 * Time : 10:48 上午
 * Description : 基于异或计算的文本加密类
 */
class StringEncrypt
{
    /**
     * @param $str string 到hex的文本
     * @return string
     */
    function strToHex(string $str): string
    {
        $hex="";
        for($i=0;$i<strlen($str);$i++)
            $hex.=dechex(ord($str[$i]));
        return strtoupper($hex);
    }


    /**
     * @param $hex string 要到文本的hex
     * @return string
     */
    function hexToStr(string $hex): string
    {
        $str="";
        for($i=0;$i<strlen($hex)-1;$i+=2)
            $str.=chr(hexdec($hex[$i].$hex[$i+1]));
        return $str;
    }

    /**
     *
     * @param $string string 需要加密的文本
     * @return string
     */
    function encode(string $string): string
    {

        $datas="%^&*()_+{}|:<>?`1234567890-=qwertyuiop[]\asdfghjkl;'zxcvbnm,./~!@#$";
        $datas=str_shuffle($datas);//每次都打乱顺序
        $arr2=str_split($datas,1);
        $arr1=str_split($string,1);
        $str="";
        foreach($arr1 as $item){
            $ok=false;
            foreach ($arr2 as $item2){
                foreach ($arr2 as $item3){
                    $i=$item2^$item3;
                    if($i===$item){
                        $str.=$item2.$item3;
                        $ok=true;
                        break;
                    }
                }
                if($ok)break;
            }
        }

        return $this->strToHex(str_rot13($str));
    }

    /**
     * @param $string string 需要解密的文本
     * @return string
     */
    function decode(string $string): string
    {
        $string=str_rot13($this->hexToStr($string));
        $arr1=str_split($string,2);
        $str="";
        foreach ($arr1 as $item){
            $arr2=str_split($item,1);
            $str.=$arr2[0]^$arr2[1];
        }
        return $str;
    }
}
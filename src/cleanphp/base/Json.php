<?php
/*
 * Copyright (c) 2023. Ankio. All Rights Reserved.
 */

/**
 * Package: cleanphp\extend\Json
 * Class Json
 * Created By ankio.
 * Date : 2022/5/5
 * Time : 12:22
 * Description :
 */

namespace cleanphp\base;

use stdClass;

class Json
{
    /**
     * @param $string string 需要解码的字符串
     * @param false $isArray 是否解码为数组
     * @return array|bool|float|int|mixed|stdClass|string|null
     */
    static function decode(string $string, bool $isArray = false)
    {
        return json_decode(self::removeUtf8Bom($string), $isArray);
    }

    static function removeUtf8Bom($text)
    {
        $bom = pack('H*', 'EFBBBF');
        return preg_replace("/^$bom/", '', $text);
    }

    /**
     * @param array $array string 需要编码的字符串
     * @return string
     */
    static function encode(array $array): string
    {
        $result = json_encode($array);
        if ($result === false) {
            Error::err(json_last_error_msg(), [], "JSON Exception");
        }
        return $result;
    }

}
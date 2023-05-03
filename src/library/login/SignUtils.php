<?php
/*******************************************************************************
 * Copyright (c) 2022. Ankio. All Rights Reserved.
 ******************************************************************************/

/**
 * Package: app\utils
 * Class SignUtils
 * Created By ankio.
 * Date : 2023/4/28
 * Time : 14:36
 * Description :
 */

namespace library\login;

class SignUtils
{
    public static function checkSign(array $args, $key): bool
    {
        if (!isset($args['sign'])) return false;
        $sign = trim($args['sign']);
        unset($args['sign']);
        return $sign === self::getSign($args, $key);
    }

    private static function getSign($args, $secretKey): string
    {
        foreach ($args as $key => $val) {
            if (empty($val)) unset($args[$key]);
        }
        ksort($args);
        $String = self::formatBizQueryParaMap($args);
        $String = $String . "&key=" . $secretKey;
        return strtoupper(hash('sha256', $String));
    }

    private static function formatBizQueryParaMap($paraMap)
    {
        $buff = "";
        ksort($paraMap);
        foreach ($paraMap as $k => $v) {
            $buff .= $k . "=" . $v . "&";
        }
        $reqPar = "";
        if (strlen($buff) > 0) {
            $reqPar = substr($buff, 0, strlen($buff) - 1);
        }
        return $reqPar;
    }

    public static function sign(array $array, $key): array
    {
        $array['sign'] = self::getSign($array, $key);
        return $array;
    }
}
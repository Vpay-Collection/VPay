<?php
/**
 * phone.php
 * Created By Dreamn.
 * Date : 2020/4/30
 * Time : 10:35 下午
 * Description : 测试心跳，推送金额
 */

$url="http://a.com";
$key="tyQD4r2iR8EMn4QW2ZdnCWnCEzeCwh287Pdz86Q3TamCiHcFTAyZmAztjGDGw6aP8cMTXQ7SajFatFa7PRb5PpsCcaPxF8Q2BTcFch6QxRCC6A2STWk33p3FMkY64HX6";
function getMillisecond() {
    list($s1, $s2) = explode(' ', microtime());
    return (float)sprintf('%.0f', (floatval($s1) + floatval($s2)) * 1000);
}
function AppHeart($url,$key){
    $_t=getMillisecond();
    $_sign=md5($_t.$key);
    $url.='/AppHeart?t='.$_t.'&sign='.$_sign;
    echo file_get_contents($url);
}
AppHeart($url,$key);
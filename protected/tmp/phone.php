<?php
/**
 * phone.php
 * Created By Dreamn.
 * Date : 2020/4/30
 * Time : 10:35 下午
 * Description : 测试心跳，推送金额
 */

$url="http://a.com";
$key="";
function AppHeart($url,$key){
    $_t=time();
    $_sign=$_t.$key;
    $url='/AppHeart?t='.$_t.'&sign='.$_sign;
    file_get_contents($url);
}
AppHeart($url,$key);
<?php

$conf = require_once 'config.php';

$alipay = new AlipaySign();

$sign = $_GET['sign'];

$_GET["key"] = $conf["key"];

$_GET = array_diff_key($_GET, array("sign" => $sign));

$_sign = $alipay->getSign($_GET, $conf["key"]);

//开始校验签名

if ($_sign === $sign) {
    echo json_encode(array("state" => true, "msg" => "支付成功", "data" => "", "count" => "0"));
} else {
    echo json_encode(array("state" => false, "msg" => "支付失败", "data" => "", "count" => "0"));
}


?>
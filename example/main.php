<?php


$conf = require_once 'config.php';

$alipay = new AlipaySign();

$_GET["key"] = $conf["key"];//把通信密钥也参与计算

$_GET["isHtml"] = 1;//采用自带的ui

$_GET["appid"] = $conf["appid"];//把appid也参与计算

$sign = $alipay->getSign($_GET, $conf["key"]);

$p = http_build_query($_GET). '&sign=' . $sign;

echo "<script>window.location.href = '" . $conf["host"] . "?" . $p . "'</script>";


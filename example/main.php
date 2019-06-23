<?php


$conf = require_once 'config.php';

$alipay = new AlipaySign();

$_GET["key"] = $conf["key"];//把通信密钥也参与计算

$sign = $alipay->getSign($_GET, $conf["key"]);

$p = http_build_query($_GET) . '&appid=' . $conf["appid"] . '&sign=' . $sign . '&isHtml=1';

echo "<script>window.location.href = '" . $conf["host"] . "?" . $p . "'</script>";


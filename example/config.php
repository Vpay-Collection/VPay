<?php
header("Content-type: text/html; charset=utf-8");
require_once 'AlipaySign.php';
return [
    "key" => "yFeWSyQ4EWEyd2zwW32B2NZDriG5PRmpEG5d72EjdSG5Kw4kHGGPRhmS2izFwA6FGhenmXQYGX68JAwADwnQAXBKhbPnrSDntmDjEMwEjEdjny6BnnsNanwPwyHiemWE",//通讯密钥
    "host" => "../../CreateOrder",//就是进行订单创建的Url
    "appid" => 10//应用id
];
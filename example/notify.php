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

    $payId=$_GET['payId'];
    $web=new web();
    $res=$web->get("../../OrderStatus?payId=$payId");
    $json=json_decode($res);
    if(isset($json->code)&&$json->code===1){
        //这是交易完成
        $web->get("../../Confirm?payId=$payId&sign=".$alipay->getSign(array("payId"=>$payId,"key"=>$conf["key"]), $conf["key"]));
        //继续业务流程
        echo json_encode(array("state" => false, "msg" => "支付失败", "data" => "", "count" => "0"));
    }else if($json->code===3){
        exit("交易已经完成");
    }


}


?>
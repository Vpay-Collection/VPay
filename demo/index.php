<?php
/**
 * Created by dreamn.
 * Date: 2019-09-28
 * Time: 21:48
 */
session_start();
include_once dirname(__FILE__).'/core/Vpay.php';
$vpay=new Vpay();

$price=0.01;//价格，对这个商品的定价，这里不一定是死的价格，可以是根据商品id查询的价格，也可以是多个商品合并计算的价格

$param=urlencode("我喜欢你呀~");//必须对字符串进行url编码，自定义参数部分应该是前端传回来的表单信息，比如json数据串，再确认订单后再将这个数据串进行解码，再插入到数据库中，即便漏单了，也可以通过后期补单进行数据入库。

$payId=$vpay->getPayId($price,$param);//支付id，直接使用内置函数生成即可，或者使用自己的方法生成

/*
 * 而且为了安全起见，价格等敏感信息应该通过后端自动提交进行签名，而不是像这个demo一样直接放在前端，放在前端只是为了让大家更好地理解
 * */

?>
<!--该文件是前端ui文件-->
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0"
          name="viewport">
    <meta content="ie=edge" http-equiv="X-UA-Compatible">
    <title>测试支付</title>
</head>
<body>

<p>商户订单号：<input id="payId" type="text" value="<?php echo $payId;?>"/></p>
<p>商户订单价：<input id="price" type="number" value="<?php echo $price;?>"/></p>
<p>自定义参数：<input id="param" type="text" value="<?php echo $param;?>"/></p>
<p>支付方式：<select id="type">
        <option value="1">微信支付</option>
        <option value="2" selected>支付宝支付</option>
    </select></p>
<p>前端支付ui：<select id="html">
        <option value="1" selected>使用vpay自己的ui</option>
        <option value="0">自己写ui支付</option>
    </select></p>
<button onclick="zf()">支付</button>
<script src="https://lib.baomitu.com/jquery/3.4.0/jquery.min.js"></script>
<script>

    function zf() {
        var p = "payId=" + $("#payId").val() + "&price=" + $("#price").val() + "&param=" + $("#param").val() + "&type=" + $("#type").val()+"&html=" + $("#html").val();
        window.location.href = "go.php?" + p;
        <!--该文件是前端ui文件，提交给后台，进行签名后发给支付页面-->
        <!--使用html就直接跳转，不使用html可以不跳转，直接get go.php然后解析获得的json数据-->
    }
</script>


</body>
</html>

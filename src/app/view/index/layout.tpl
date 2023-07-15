<!DOCTYPE html>
<html lang="{$__lang}">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"/>
    <meta http-equiv="x-ua-compatible" content="ie=edge"/>
    <meta name="description" content="VPay是一款让个人收款变得轻松的应用。利用安卓设备上报数据，快速接受付款，安全可靠。快捷、简单的个人收款解决方案，让您更便利地收款。">
    <meta name="keywords" content="Vpay,支付,免签,微信,支付宝,个人收款,免签约,V免签"/>
    <title>Vpay {$__version}</title>
    {include file="layout_headers"}
</head>


<body class="bg-{$theme} bg-gradient" id="app">

<div id="loadingOverlay">
    <figure>
        <div class="dot white"></div>
        <div class="dot"></div>
        <div class="dot"></div>
        <div class="dot"></div>
        <div class="dot"></div>
    </figure>
    <p></p>
</div>


<!-- Container for demo purpose -->
{include file=$__template_file}

</body>




</html>

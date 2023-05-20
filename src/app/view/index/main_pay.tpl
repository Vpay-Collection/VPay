<style>
    .product-image {
        width: 150px;
    }
</style>
<header>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white fixed-top">
        <div class="container container-fluid justify-content-between align-items-center">
            <a href="/" class="text-reset">
                <div class="d-flex align-items-center mb-sm-0 mb-xm-2">

                    <div class="col-auto me-3">
                        <img src="{$app['app_image']}" height="35"/>
                    </div>
                    <div class="col d-none d-lg-block me-3">
                        <h5 class="m-0">{$app["app_name"]}收银台</h5>
                    </div>

                </div>
            </a>
            {* <form class="d-flex align-items-center mb-sm-0 mb-xm-2 ">
                 <input autocomplete="off" type="search" class="form-control rounded" placeholder="搜索商品"/>
                 <button type="button" class="input-group-text border-0"><i class="fas fa-search"></i></button>
             </form>*}

            <div class="d-flex align-items-center mb-sm-0 mb-xm-2 ms-auto me-0">
                <a class="btn btn-dark px-3" href="https://github.com/Vpay-Collection/VPay" role="button">
                    <i class="fab fa-github"></i>
                </a>
            </div>
        </div>
    </nav>

    <!-- Navbar -->

    <!-- Jumbotron -->
    <div style="margin-top: 58px"></div>
    <!-- Jumbotron -->
</header>
<!-- Container for demo purpose -->
<div class="container my-5">

    <!--Section: Design Block-->
    <section class="mb-10">
        <div class="row">
            <div class="col-lg-7 pe-lg-4 mb-5 mb-lg-0">
                <div class="card p-4">
                    <h4 class="mb-4">
                        {if $pay_type === 1}
                            <i class="fab fa-alipay  me-2 text-primary"></i>
                            支付宝扫码支付
                        {elseif $pay_type === 2}
                            <i class="fab fa-weixin me-2 text-success"></i>
                            微信扫码支付
                        {else}
                            <i class="fab fa-qq me-2 text-info"></i>
                            QQ扫码支付
                        {/if}
                    </h4>
                    <div class="text-center">
                        <p class="h4 mb-1 text-danger wait-pay">￥{number_format($real_price,2)}</p>
                        <img class="wait-pay" src="{url("api","image","qrcode",['url'=>$pay_image])}"
                             style="max-width: 200px" alt="支付码">
                        <div id="time" class="mb-3 wait-pay display-2 text-muted">
                            00:00
                        </div>
                        <div>
                            <div class="alert" role="alert" data-mdb-color="danger">
                                <span id="error_msg_body">
                                    请在倒计时结束之前扫描二维码并输入<b>{number_format($real_price,2)}</b>完成支付。<b>超时后请勿支付！</b>
                                </span>
                                <br>如有疑问，请发邮件联系<a
                                        href="mailto:{$mail}">{$mail}</a>。
                            </div>
                        </div>
                    </div>
                    <div>

                    </div>
                </div>
            </div>
            <div class="col-lg-5 ps-lg-4">
                <div class="card  p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="mb-md-0">订单信息</h4>
                    </div>
                    <div class="d-flex justify-content-start border-bottom  mb-4 pb-4">
                        <div class="w-100 ps-md-4">
                            <div class="d-flex justify-content-between">
                                <div class="">
                                    <p class="h6 pt-1 mb-1">商品</p>
                                    <p class="h6 pt-1 mb-1">金额</p>
                                    <p class="h6 pt-1 mb-1">创建时间</p>
                                </div>
                                <div>
                                    <p class="h6 pt-1 mb-1">{$app_item}</p>
                                    <p class="h6 pt-1 mb-1 text-danger">￥{number_format($price,2)}</p>
                                    <p class="h6 pt-1 text-muted mb-1">{date("Y-m-d H:i:s",$create_time)}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            {$money = number_format($price - $real_price,2)}
                            {if $money > 0}
                                <div class="d-flex justify-content-between">
                                    <p class="mb-2">优惠</p>
                                    <p class="mb-2 text-success">￥{$money}</p>
                                </div>
                            {elseif $money<0}
                                <div class="d-flex justify-content-between">
                                    <p class="mb-2">溢价</p>
                                    <p class="mb-2 text-danger">￥{$money}</p>
                                </div>
                            {/if}

                            <div class="d-flex justify-content-between">
                                <h4>总计</h4>
                                <h4>￥{number_format($real_price,2)}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--Section: Design Block-->

</div>

<!-- Container for demo purpose -->
{include file="layout_scripts"}
<script>
    var start = parseInt("{$create_time}");
    var timeout = parseInt("{$timeout}");
</script>
<script src="../../public/app/pay.js?v={$__version}" defer></script>

{include file="layout_footer"}
<!-- Container for demo purpose -->
<div class="container my-5 py-5">

    <!-- Section: Design Block -->
    <section class="mb-3">
        <h5 class="mb-4">收入统计</h5>

        <div class="row">
            <div class="col-md-6 mb-4 mb-md-0">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="p-3 badge-primary rounded-4">
                                    <i class="fas fa-money-bill-1-wave"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-4">
                                <p class="text-muted mb-1">当天收入</p>
                                <h2 class="mb-0">

                                    {$today_price}
                                    <span class="text-danger" style="font-size: 0.875rem"><i
                                                class="fas fa-dollar-sign"></i></span>
                                </h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-4 mb-md-0">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="p-3 badge-primary rounded-4">
                                    <i class="fas fa-money-bill-1"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-4">
                                <p class="text-muted mb-1">总计收入</p>
                                <h2 class="mb-0">

                                    {$total_price}
                                    <span class="text-danger" style="font-size: 0.875rem"><i
                                                class="fas fa-dollar-sign"></i></span>
                                </h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Section: Design Block -->


    <section class="mb-8">


        <div class="card mb-4">
            <div class="card-header py-3  border-0">
                <strong>最近订单</strong>
                <a href="{url('admin','order','index')}" class="btn btn-primary float-end">详情</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover text-nowrap">
                        <thead>
                        <tr>
                            <th scope="col">商户</th>
                            <th scope="col">商品</th>
                            <th scope="col">金额</th>
                            <th scope="col">支付时间</th>
                        </tr>
                        </thead>
                        <tbody>
                        {foreach $payments as $order}
                            <tr>
                                <td>{$order['app_name']}</td>
                                <td>{$order['app_item']}</td>
                                <td>￥{$order['real_price']}</td>
                                <td>{date("Y-m-d H:i:s",$order['pay_time'])}</td>
                            </tr>
                        {/foreach}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header border-0 py-3">
                <strong>订单走势</strong>
            </div>
            <div class="card-body">
                <canvas
                        data-mdb-chart="line"
                        data-mdb-dataset-label="收入趋势"
                        data-mdb-labels="[{foreach $day as $item}'{$item}'{if $item@last}{else},{/if}{/foreach}]"
                        data-mdb-dataset-data="[{foreach $data as $item}'{$item}'{if $item@last}{else},{/if}{/foreach}]"
                ></canvas>
            </div>
        </div>

    </section>
    <!--Section: Design Block-->


</div>
<!-- Container for demo purpose -->

{include file="layout_scripts"}
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
                            <th scope="col"></th>
                            <th scope="col">Product Detail Views</th>
                            <th scope="col">Unique Purchases</th>
                            <th scope="col">Quantity</th>
                            <th scope="col">Product Revenue</th>
                            <th scope="col">Avg. Price</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <th scope="row">Value</th>
                            <td>18,492</td>
                            <td>228</td>
                            <td>350</td>
                            <td>$4,787.64</td>
                            <td>$13.68</td>
                        </tr>
                        <tr>
                            <th scope="row">Percentage change</th>
                            <td>
                  <span class="text-danger">
                    <i class="fas fa-caret-down me-1"></i><span>-48.8%%</span>
                  </span>
                            </td>
                            <td>
                  <span class="text-success">
                    <i class="fas fa-caret-up me-1"></i><span>14.0%</span>
                  </span>
                            </td>
                            <td>
                  <span class="text-success">
                    <i class="fas fa-caret-up me-1"></i><span>46.4%</span>
                  </span>
                            </td>
                            <td>
                  <span class="text-success">
                    <i class="fas fa-caret-up me-1"></i><span>29.6%</span>
                  </span>
                            </td>
                            <td>
                  <span class="text-danger">
                    <i class="fas fa-caret-down me-1"></i><span>-11.5%</span>
                  </span>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">Absolute change</th>
                            <td>
                  <span class="text-danger">
                    <i class="fas fa-caret-down me-1"></i><span>-17,654</span>
                  </span>
                            </td>
                            <td>
                  <span class="text-success">
                    <i class="fas fa-caret-up me-1"></i><span>28</span>
                  </span>
                            </td>
                            <td>
                  <span class="text-success">
                    <i class="fas fa-caret-up me-1"></i><span>111</span>
                  </span>
                            </td>
                            <td>
                  <span class="text-success">
                    <i class="fas fa-caret-up me-1"></i><span>$1,092.72</span>
                  </span>
                            </td>
                            <td>
                  <span class="text-danger">
                    <i class="fas fa-caret-down me-1"></i><span>$-1.78</span>
                  </span>
                            </td>
                        </tr>
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
                        data-mdb-dataset-label="Traffic"
                        data-mdb-labels="['Monday', 'Tuesday' , 'Wednesday' , 'Thursday' , 'Friday' , 'Saturday' , 'Sunday ']"
                        data-mdb-dataset-data="[2112, 2343, 2545, 3423, 2365, 1985, 987]"
                ></canvas>
            </div>
        </div>

    </section>
    <!--Section: Design Block-->


</div>
<!-- Container for demo purpose -->

{include file="layout_scripts"}
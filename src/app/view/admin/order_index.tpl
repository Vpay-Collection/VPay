<div class="container my-5 py-5">

    <!-- Section: Design Block -->
    <section class="mb-10">


        <div class="row">
            <div class="col-12 mb-2">
                <div class="card">
                    <div class="card-body">
                        <div class="row ">
                            <div class="col">
                                <select class="select" name="app" id="app">
                                    <option value="">不区分</option>
                                    {foreach $app as $item}
                                        <option value="{$item.id}">{$item.app_name}</option>
                                    {/foreach}
                                </select>
                                <label class="form-label select-label" for="app">应用</label>
                            </div>
                            <div class="col">
                                <div class="form-outline">
                                    <input type="text" id="form_name" class="form-control" name="name"/>
                                    <label class="form-label" for="form_name">商品名称</label>
                                </div>
                            </div>
                            <div class="col">
                                <select class="select" name="status" id="status">
                                    <option value="">不区分</option>
                                    <option value="1">等待支付</option>
                                    <option value="2">已支付</option>
                                    <option value="3">订单已确认</option>
                                    <option value="-1">已关闭</option>
                                </select>
                                <label class="form-label select-label" for="status">订单状态</label>
                            </div>
                            <div class="col">
                                <button type="button" id="search" class="btn btn-primary">查找</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div id="datatable"
                             data-mdb-loading="true"
                             data-mdb-rows-text="每一页显示数量："
                             data-mdb-no-found-message="空空如也"
                             data-mdb-of-text="/"
                             data-mdb-pagination="false"
                             data-mdb-striped="true"
                        ></div>
                        <div id="pagination" style="margin: 0 auto" class="mt-3" data-previous="上一页"
                             data-next="下一页"></div>
                    </div>
                </div>
            </div>
        </div>

    </section>
</div>

<div
        class="alert fade"
        id="success_msg"
        role="alert"
        data-mdb-color="success"
        data-mdb-position="top-right"
        data-mdb-stacking="true"
        data-mdb-width="200px"
        data-mdb-append-to-body="true"
        data-mdb-hidden="true"
        data-mdb-autohide="true"
        data-mdb-delay="2000"
>
    <i class="fas fa-times-circle me-3"></i>
    <span id="success_msg_body"></span>
</div>


{include file="layout_scripts"}

<script src="../../public/app/order.js?v={$__version}" defer></script>
<!-- Container for demo purpose -->
<div class="container my-5 pb-5">

    <!-- Section: Design Block -->
    <section class="mb-10">

        <div class="card-body ps-2 pb-4">
            <h3 class="">收入统计</h3>
        </div>

            <div class="col-12 mb-2">
                <div class="card ">
                    <div class="card-header py-3">
                        <strong>邮件配置</strong>
                    </div>
                    <div class="card-body text-center">


                        <form action="">

                            {if $sso==false}
                                <div class="form-outline mb-4">
                                    <input type="text" name="smtp" id="smtp" class="form-control"
                                           value="{$smtp}"/>
                                    <label class="form-label" for="smtp">SMTP服务器</label>
                                </div>
                                <div class="form-outline mb-4">
                                    <input type="email" name="send" id="send" class="form-control"
                                           value="{$send}"/>
                                    <label class="form-label" for="send">发件人邮箱</label>
                                </div>
                                <div class="form-outline mb-4">
                                    <input type="text" name="passwd" id="passwd" class="form-control"
                                           value="{$passwd}"/>
                                    <label class="form-label" for="passwd">发件人邮箱密码</label>
                                </div>
                                <div class="form-outline mb-4">
                                    <input type="number" name="port" id="port" class="form-control"
                                           value="{$port}"/>
                                    <label class="form-label" for="port">邮箱端口</label>
                                </div>
                            {/if}
                            <div class="form-outline mb-4">
                                <input type="email" name="received" id="received" class="form-control"
                                       value="{$received}"/>
                                <label class="form-label" for="received">收件人邮箱</label>
                            </div>
                            <div class="form-check mb-4 text-start">
                                <input class="form-check-input" name="pay_success" type="checkbox"
                                       value="1"
                                       {if $pay_success}checked="checked"{/if} id="pay_success"/>
                                <label class="form-check-label"
                                       for="pay_success">接收用户支付成功的通知</label>
                            </div>
                            <div class="form-check mb-4 text-start">
                                <input class="form-check-input" type="checkbox" value="1"
                                       {if $pay_daily}checked="checked"{/if} name="pay_daily" id="pay_daily"/>
                                <label class="form-check-label"
                                       for="pay_daily">接收收益日报</label>
                            </div>
                            <button type="submit" class="btn btn-primary mb-2" id="save">
                                保存信息
                            </button>
                            <button type="button" class="btn btn-outline-secondary mb-2" data-mdb-toggle="modal"
                                    data-mdb-target="#confirm" id="test">测试邮箱
                            </button>
                        </form>
                    </div>
                </div>
            </div>


        </div>
    </section>
    <!--Section: Profile-->

</div>


<div class="modal fade" id="confirm" tabindex="-1" aria-labelledby="exampleModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">邮件测试结果</h5>
                <button type="button" class="btn-close" data-mdb-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="testData">
            </div>
            <div class="modal-footer">

                <button type="button" class="btn btn-primary" id="cancel"
                        data-mdb-dismiss="modal">好的
                </button>
            </div>
        </div>
    </div>
</div>


{include file="layout_scripts"}

<script src="../../public/app/mail.js?v={$__version}" defer></script>
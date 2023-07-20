<!-- Container for demo purpose -->
<div class="container my-5 pb-5">

    <!-- Section: Design Block -->
    <section class="mb-10">

        <div class="card-body ps-2 pb-4">
            <h3 class="">通知配置</h3>
        </div>

        <div class="col-12 mb-2">
            <div class="card ">
                <div class="card-header py-3">
                    <strong><i class="fas fa-envelope me-3"></i>邮件服务器配置</strong>
                </div>
                <div class="card-body text-center">


                    <form action="">
                        <div class="form-check mb-4 text-start">
                            <input class="form-check-input" type="checkbox" value="1" name="sso"
                                   id="sso"/>
                            <label class="form-check-label d-flex align-items-center"
                                   for="sso">使用SSO模式<span class="badge badge-primary ms-2">内部使用</span></label>
                        </div>
                        <div id="mail_area">
                            <div class="form-outline mb-4">
                                <input type="text" name="smtp" id="smtp" class="form-control"/>
                                <label class="form-label" for="smtp">SMTP服务器</label>
                            </div>
                            <div class="form-outline mb-4">
                                <input type="email" name="user" id="send" class="form-control"/>
                                <label class="form-label" for="send">发件人邮箱</label>
                            </div>
                            <div class="form-outline mb-4">
                                <input type="text" name="password" id="password" class="form-control"/>
                                <label class="form-label" for="password">发件人邮箱密码</label>
                            </div>
                            <div class="form-outline mb-4">
                                <input type="number" name="port" id="port" class="form-control"/>
                                <label class="form-label" for="port">邮箱端口</label>
                            </div>
                        </div>

                        <div id="sso_area">
                            <div class="form-outline mb-4">
                                <input type="text" name="url" id="url" class="form-control"/>
                                <label class="form-label" for="url">服务器地址</label>
                            </div>
                            <div class="form-outline mb-4">
                                <input type="text" name="id" id="id" class="form-control"/>
                                <label class="form-label" for="id">授权ID</label>
                            </div>
                            <div class="form-outline mb-4">
                                <input type="text" name="key" id="key" class="form-control"/>
                                <label class="form-label" for="key">授权Key</label>
                            </div>
                        </div>


                        <div class="form-outline mb-4">
                            <input type="email" name="admin" id="admin" class="form-control"/>
                            <label class="form-label" for="admin">收件人邮箱</label>
                        </div>
                        <div class="form-check mb-4 text-start">
                            <input class="form-check-input" name="success_notice" type="checkbox"
                                   value="1" id="success_notice"/>
                            <label class="form-check-label"
                                   for="success_notice">接收用户支付成功的通知</label>
                        </div>
                        <div class="form-check mb-4 text-start">
                            <input class="form-check-input" type="checkbox" value="1" name="daily_notice"
                                   id="pay_daily"/>
                            <label class="form-check-label"
                                   for="pay_daily">接收收益日报</label>
                        </div>
                        <div class="form-check mb-4 text-start">
                            <input class="form-check-input" type="checkbox" value="1" name="update_notice"
                                   id="update_notice"/>
                            <label class="form-check-label"
                                   for="update_notice">接收更新提醒</label>
                        </div>
                        <button type="submit" class="btn btn-primary mb-2" id="save">
                            保存信息
                        </button>
                        <button type="button" class="btn btn-secondary mb-2" data-mdb-toggle="modal"
                                data-mdb-target="#confirm" id="test">测试邮箱
                        </button>
                    </form>
                </div>
            </div>
        </div>


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
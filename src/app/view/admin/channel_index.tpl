<div class="container my-5 py-5">

    <!--Section: Profile-->
    <section class="mb-10">
        <div class="row">
            <div class="col-12 mb-2">
                <div class="card ">
                    <div class="card-header py-3"><strong>App监控状态</strong></div>
                    <div class="card-body text-center pt-4">
                        <div class="row">
                            <div class="col-md-4 mb-4 mb-md-0">
                                <img src="{$qrcode}" id="qrcode" class="mb-4" style="max-width: 250px; width: 100%;"/>
                                {if $online}
                                    <div class="alert" role="alert" data-mdb-color="success">
                                        <span class="badge badge-primary me-2">{$last_heart}</span> 心跳正常
                                    </div>
                                {else}
                                    <div class="alert" role="alert" data-mdb-color="warning">
                                        <span class="badge badge-primary">{$last_heart}</span> 请检查手机端监控状态
                                    </div>
                                {/if}
                            </div>
                            <div class="col-md-8 text-start">
                                <div class="note note-primary mb-3">
                                    <strong>重要:</strong> 请先下载安装安卓监控客户端
                                    <a href="https://github.com/Vpay-Collection/vpay-android" class="btn btn-primary">Github下载</a>
                                </div>
                                <form class="mt-4" id="form_app">
                                    <!-- Email input -->
                                    <div class="form-outline mb-4">
                                        <input type="text" id="key" name="key" class="form-control" value="{$key}"/>
                                        <label class="form-label" for="key">通讯密钥</label>
                                    </div>
                                    <div class="form-outline mb-4">
                                        <input type="text" id="timeout" name="timeout" class="form-control"
                                               value="{$timeout}"/>
                                        <label class="form-label" for="timeout">订单超时时间（单位：分钟）</label>
                                    </div>
                                    <div class="row mb-4">
                                        <select class="select" name="conflict" id="conflict">
                                            <option value="1" {if $conflict=="1"}selected{/if}>金额递增</option>
                                            <option value="2" {if $conflict=="2"}selected{/if}>金额递减</option>
                                        </select>
                                        <label class="form-label select-label" for="conflict">账单冲突解决方案</label>
                                    </div>


                                    <!-- Submit button -->
                                    <div class="text-center">
                                        <button type="submit" class="btn btn-primary">保存</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 mb-2">
                <div class="card ">
                    <div class="card-header py-3">
                        <strong>收款码配置</strong>
                    </div>
                    <div class="card-body text-center">
                        <div class="alert" role="alert" data-mdb-color="danger">
                            上传完成请点击保存完成配置
                        </div>
                        <div class="row">
                            <div class="col-lg-3">
                                <div class="d-flex justify-content-center mb-4">
                                    <div class="file-upload-wrapper shadow-5"
                                         style="max-width: 300px">
                                        <input type="file" class="file-upload-input" id="alipay"
                                               data-mdb-default-file="{$alipay}"
                                               data-mdb-multiple="false"
                                               data-mdb-remove-btn="删除"
                                               data-mdb-accepted-extensions="image/*"
                                               data-mdb-preview-msg="上传支付宝收款码"
                                               data-mdb-default-msg="上传支付宝收款码"
                                               data-mdb-format-error="不支持该文件 (支持的格式为 ~~~)"
                                               data-mdb-file-upload="file-upload"/>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="d-flex justify-content-center mb-4">
                                    <div class="file-upload-wrapper shadow-5"
                                         style="max-width: 300px">
                                        <input type="file" class="file-upload-input" id="wechat"
                                               data-mdb-default-file="{$wechat}"
                                               data-mdb-multiple="false"
                                               data-mdb-remove-btn="删除"
                                               data-mdb-accepted-extensions="image/*"
                                               data-mdb-preview-msg="上传微信收款码"
                                               data-mdb-default-msg="上传微信收款码"
                                               data-mdb-format-error="不支持该文件 (支持的格式为 ~~~)"
                                               data-mdb-file-upload="file-upload"/>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="d-flex justify-content-center mb-4">
                                    <div class="file-upload-wrapper shadow-5"
                                         style="max-width: 300px">
                                        <input type="file" class="file-upload-input" id="union"
                                               data-mdb-default-file="{$union}"
                                               data-mdb-multiple="false"
                                               data-mdb-remove-btn="删除"
                                               data-mdb-accepted-extensions="image/*"
                                               data-mdb-preview-msg="上传云闪付收款码"
                                               data-mdb-default-msg="上传云闪付收款码"
                                               data-mdb-format-error="不支持该文件 (支持的格式为 ~~~)"
                                               data-mdb-file-upload="file-upload"/>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="d-flex justify-content-center mb-4">
                                    <div class="file-upload-wrapper shadow-5"
                                         style="max-width: 300px">
                                        <input type="file" class="file-upload-input" id="digital"
                                               data-mdb-default-file="{$digital}"
                                               data-mdb-multiple="false"
                                               data-mdb-remove-btn="删除"
                                               data-mdb-accepted-extensions="image/*"
                                               data-mdb-preview-msg="上传数字人民币收款码"
                                               data-mdb-default-msg="上传数字人民币收款码"
                                               data-mdb-format-error="不支持该文件 (支持的格式为 ~~~)"
                                               data-mdb-file-upload="file-upload"/>
                                    </div>
                                </div>
                            </div>

                            <div class="text-center  mb-2">
                                <button type="button" class="btn btn-primary" id="updateInfo">
                                    更新收款码
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

    </section>
    <!--Section: Profile-->

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
    <i class="fas fa-check-circle me-3"></i>
    <span id="success_msg_body"></span>
</div>

<div
        class="alert fade"
        id="error_msg"
        role="alert"
        data-mdb-color="danger"
        data-mdb-position="top-right"
        data-mdb-stacking="true"
        data-mdb-width="200px"
        data-mdb-append-to-body="true"
        data-mdb-hidden="true"
        data-mdb-autohide="true"
        data-mdb-delay="2000"
>
    <i class="fas fa-times-circle me-3"></i>
    <span id="error_msg_body"></span>
</div>


{include file="layout_scripts"}
{*TODO 此处需要完善js逻辑*}
<script src="../../public/app/channel.js?v={$__version}" defer></script>
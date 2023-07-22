<!-- Container for demo purpose -->
<div class="container my-5 pb-5">

    <!-- Section: Design Block -->
    <section class="mb-10">

        <div class="card-body ps-2 pb-4">
            <h3 class="">App监控</h3>
        </div>
        <div class="row">
            <div class="col-12 mb-2">
                <div class="card ">
                    <div class="card-header py-3"><strong><i class="fas fa-mobile-screen-button me-3"></i>手机状态</strong></div>
                    <div class="card-body text-center pt-4">
                        <div class="row">
                            <div class="col-xl-12">
                                <img src="{$qrcode}" id="qrcode" class="mb-4" style="max-width: 250px; width: 100%;"/>

                            </div>
                            <div class="col-xl-12 text-start">
                                {if $status==0}
                                    <div class="alert" role="alert" data-mdb-color="primary">
                                        <i class="fas fa-info-circle me-3"></i>安卓监控客户端 <a
                                                class="btn text-white ms-2" style="background-color: #333333;"
                                                target="_blank" href="https://github.com/Vpay-Collection/vpay-android"
                                                role="button">
                                            <i class="fab fa-github me-2" style="color: white"></i> Github下载
                                        </a>
                                    </div>
                                    <div class="alert" role="alert" data-mdb-color="warning">
                                        <i class="fas fa-exclamation-triangle me-3"></i>请使用<b>安卓监控客户端</b>扫码绑定。
                                    </div>
                                {elseif $status==1}
                                    <div class="alert" role="alert" data-mdb-color="danger">
                                        <i class="fas fa-times-circle me-3"></i>
                                        请检查手机端监控状态，最后心跳时间为：{$last_heart}
                                    </div>
                                {else}
                                    <div class="alert" role="alert" data-mdb-color="success">
                                        <i class="fas fa-check-circle me-3"></i>心跳正常
                                    </div>
                                {/if}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 mb-2">
                <div class="card ">
                    <div class="card-header py-3"><strong><i class="fas fa-gear me-3"></i>监控与收款码配置</strong></div>
                    <div class="card-body text-center pt-4">
                        <div class="row">
                            <form class="mt-4" id="form_app">
                                <!-- Email input -->
                                <div class="form-outline mb-4">
                                    <input type="text" id="key" name="key" class="form-control" />
                                    <label class="form-label" for="key">通讯密钥</label>
                                </div>
                                <div class="form-outline mb-4">
                                    <input type="text" id="timeout" name="timeout" class="form-control"
                                          />
                                    <label class="form-label" for="timeout">订单超时时间（单位：分钟）</label>
                                </div>
                                <div class="row mb-4">
                                    <select class="select" name="conflict" id="conflict">
                                        <option value="1" >金额递增</option>
                                        <option value="2">金额递减</option>
                                    </select>
                                    <label class="form-label select-label" for="conflict">账单冲突解决方案</label>
                                </div>

                                <div class="row mb-4">
                                    <div class="col-lg-6">
                                        <div class="d-flex justify-content-center mb-4">
                                            <div class="file-upload-wrapper shadow-5"
                                                 style="width: 200px">
                                                <input type="file" class="file-upload-input" id="alipay" name="image_alipay"
                                                       data-mdb-default-file=""
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
                                    <div class="col-lg-6">
                                        <div class="d-flex justify-content-center mb-4">
                                            <div class="file-upload-wrapper shadow-5"
                                                 style="width: 200px">
                                                <input type="file" class="file-upload-input" id="wechat" name="image_wechat"
                                                       data-mdb-default-file=""
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

    <!--Section: Profile-->

</div>
{include file="layout_scripts"}

<script src="../../public/app/channel.js?v={$__version}" defer></script>
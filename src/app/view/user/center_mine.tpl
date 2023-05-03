<div class="container my-5 py-5">

    <!--Section: Profile-->
    <section class="mb-10">
        <div class="row">
            <div class="col-12 mb-2">
                <div class="card ">
                    <div class="card-header py-3">
                        <strong>{lang("编辑个人信息")}</strong>
                    </div>
                    <div class="card-body text-center">
                        <div class="mt-1 mb-4">
                            <strong>{lang("个人头像")}</strong>
                        </div>

                        <form action="">
                            <div class="d-flex justify-content-center mb-4">
                                <div id="dnd-default-value" class="file-upload-wrapper shadow-5"
                                     style="max-width: 300px">
                                    <input type="file" class="file-upload-input" id="image"
                                           data-mdb-default-file="{$user['image']}"
                                           data-mdb-multiple="false"
                                           data-mdb-remove-btn="{lang('删除')}"
                                           data-mdb-accepted-extensions="image/*"
                                           data-mdb-preview-msg="{lang('拖拽到此或点击这里进行上传')}"
                                           data-mdb-default-msg="{lang('拖拽到此或点击这里进行上传')}"
                                           data-mdb-format-error="{lang('不支持该文件 (支持的格式为 ~~~)')}"
                                           data-mdb-file-upload="file-upload"/>
                                </div>
                            </div>

                            <div class="form-outline mb-4">
                                <input type="text" name="nickname" id="formName" class="form-control"
                                       value="{$user['nickname']}"/>
                                <label class="form-label" for="formName">{lang("昵称")}</label>
                            </div>

                            <div class="form-outline mb-4">
                                <input type="email" name="mail" id="formEmail" class="form-control"
                                       value="{$user['mail']}"/>
                                <label class="form-label" for="formEmail">{lang("邮箱")}</label>
                            </div>
                            <div class="form-check mb-4 text-start">
                                <input class="form-check-input" type="checkbox" value=""
                                       {if $user['update']}checked="checked"{/if} id="flexCheckDefault"/>
                                <label class="form-check-label"
                                       for="flexCheckDefault">{lang("使用微信登录时，自动使用微信头像和微信昵称更新信息。")}</label>
                            </div>

                            <button type="button" class="btn btn-primary mb-2" id="updateInfo">
                                {lang("更新信息")}
                            </button>
                            {if $user.id !== 1}
                                <button type="button" class="btn btn-outline-secondary mb-2" data-mdb-toggle="modal"
                                        data-mdb-target="#confirm">{lang("注销")}</button>
                            {/if}
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="card ">
                    <div class="card-header py-3"><strong>{lang("绑定其他登录方式")}</strong></div>
                    <div class="card-body text-center pt-4">

                        <button type="button" id="bind-wechat" class="btn btn-primary mb-2" data-mdb-toggle="modal"
                                data-mdb-target="#wechat"><i class="fas fa-qrcode me-2"></i>{lang("重绑定微信")}
                        </button>
                        <button type="button" id="bind-finger" class="btn btn-outline-secondary mb-2"><i
                                    class="fas fa-fingerprint me-2"></i>{lang("重绑定指纹识别")}</button>

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

<div class="modal fade" id="confirm" tabindex="-1" aria-labelledby="exampleModalLabel"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{lang("注销确认")}</h5>
                <button type="button" class="btn-close" data-mdb-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                {lang("您即将注销您在《Ankioの用户中心》的所有账号，注销后您将失去所有账号数据，一旦您确认注销，账号即刻删除无法找回。")}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-mdb-dismiss="modal">
                    {lang("我再想想")}
                </button>
                <button type="button" class="btn btn-primary" id="cancel"
                        data-mdb-dismiss="modal">{lang("确定注销")}</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="wechat" tabindex="-1" aria-labelledby="exampleModalLabel"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{lang("微信绑定")}</h5>
                <button type="button" class="btn-close" data-mdb-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-12">
                        <div id="image_loading" class="bg-image" style="height: 300px;width: 300px;margin: 0 auto">

                            <img
                                    style="height: 300px;width: 300px;"
                                    src=""
                                    class="placeholder img-fluid"
                                    alt="wechat qr"
                                    id="qr_img"
                            />
                            <div id="image_mask" class="mask" style="background-color: rgba(0, 0, 0, 0.6);">
                                <div class="d-flex justify-content-center align-items-center h-100">
                                    <p class="text-white mb-0" id="image_mask_title"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>
</div>


{include file="layout_scripts"}
<script src="../../public/app/login-wechat.js?v={$__version}" defer></script>
<script src="../../public/app/login-finger.js?v={$__version}" defer></script>
<script src="../../public/app/mine.js?v={$__version}" defer></script>
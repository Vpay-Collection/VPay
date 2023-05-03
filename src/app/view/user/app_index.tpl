<!-- Container for demo purpose -->
<div class="container my-5 py-5">

    <!-- Section: Design Block -->
    <section class="mb-10">


        <div class="row">
            <div class="col-12 mb-2">
                <div class="card">
                    <div class="card-body">
                        <button type="button" id="addApp" class="btn btn-primary" data-mdb-toggle="modal"
                                data-mdb-target="#addOrUpdate">{lang("添加应用")}</button>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div id="datatable"
                             data-mdb-loading="true"
                             data-mdb-rows-text="{lang('每一页显示数量：')}"
                             data-mdb-no-found-message="{lang('空空如也')}"
                             data-mdb-of-text="{lang('/')}"
                             data-mdb-pagination="false"
                             data-mdb-striped="true"
                        ></div>
                        <div id="pagination" style="margin: 0 auto" class="mt-3" data-previous="{lang("上一页")}"
                             data-next="{lang("下一页")}"></div>
                    </div>
                </div>
            </div>
        </div>

    </section>
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

<div class="modal fade" id="addOrUpdate" tabindex="-1" aria-labelledby="addOrUpdateTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable"
    ">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="addOrUpdateTitle" data-add="{lang("新增应用")}"
                data-change="{lang("修改应用")}">{lang("新增应用")}</h5>
            <button type="button" class="btn-close" data-mdb-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <form class="form form-vertical " id="form">
                <div class="form-outline mb-4 d-none">
                    <input type="text" class="form-control" id="form_id" name="id" placeholder="" value=""/>
                    <label class="form-label" for="form_id"></label>
                </div>
                <div class="form-outline mb-4">
                    <input type="text" class="form-control" id="form_title" name="title"/>
                    <label class="form-label" for="form_title">{lang("网站名称")}</label>
                </div>
                <div class="form-outline mb-4">
                    <input type="text" class="form-control" id="form_domain" name="domain"/>
                    <label class="form-label" for="form_domain">{lang("网站域名")}</label>
                </div>
                <div class="file-upload-wrapper mb-4 ">
                    <input
                            id="file-upload"
                            type="file"
                            name="icon"
                            data-mdb-file-upload="file-upload"
                            class="file-upload-input"
                            data-mdb-multiple="false"
                            data-mdb-remove-btn="{lang('删除')}"
                            data-mdb-accepted-extensions="image/*"
                            data-mdb-preview-msg="{lang('拖拽到此或点击这里进行上传')}"
                            data-mdb-default-msg="{lang('拖拽到此或点击这里进行上传')}"
                            data-mdb-format-error="{lang('不支持该文件 (支持的格式为 ~~~)')}"
                    />
                </div>

                <div class="form-outline mb-4">
                    <input type="number" class="form-control" id="form_client_count" name="client_count"/>
                    <label class="form-label" for="form_client_count">{lang("客户端数量")}</label>
                </div>
                <div class="form-outline mb-4">
                    <input type="number" class="form-control" id="form_time" name="time" placeholder=""/>
                    <label class="form-label" for="form_time">{lang("授权时间（单位：秒）")}</label>
                </div>
                <div class="col-12">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="auto" id="form_auto"/>
                        <label class="form-check-label" for="form_auto">
                            {lang("自动授予访问权限")}
                        </label>
                    </div>
                </div>

                <div class="col-12">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="apply" id="form_apply"/>
                        <label class="form-check-label" for="form_apply">
                            {lang("允许申请访问权限")}
                        </label>
                    </div>
                </div>


            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-mdb-dismiss="modal">{lang("关闭")}</button>
            <button type="button" class="btn btn-primary" id="saveOrUpdate"
                    data-mdb-dismiss="modal">{lang("保存")}</button>
        </div>
    </div>
</div>


{include file="layout_scripts"}

<script src="../../public/app/app.js?v={$__version}" defer></script>
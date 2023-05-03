<div class="container my-5 py-5">

    <!-- Section: Design Block -->
    <section class="mb-10">


        <div class="row">
            <div class="col-12 mb-2">
                <div class="card">
                    <div class="card-body">
                        <div class="row ">
                            <div class="col">
                                <div class="form-outline">
                                    <input type="text" id="form_name" class="form-control" name="name"/>
                                    <label class="form-label" for="form_name">{lang("用户昵称")}</label>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-outline">
                                    <input type="text" id="form_mail" class="form-control" name="mail"/>
                                    <label class="form-label" for="form_mail">{lang("用户邮箱")}</label>
                                </div>
                            </div>
                            <div class="col">
                                <button type="button" id="search" class="btn btn-primary">{lang("查找")}</button>
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

<script src="../../public/app/user.js?v={$__version}" defer></script>
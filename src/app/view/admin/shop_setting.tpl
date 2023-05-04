<link rel="stylesheet" href="../../public/plugins/css/wysiwyg.min.css?v={$__version}" media="none"
      onload="this.media='all'">
<div class="container my-5 py-5">

    <!--Section: Profile-->
    <section class="mb-10">
        <div class="row">

            <div class="col-12 mb-2">
                <div class="card ">
                    <div class="card-header py-3">
                        <strong>内置商城配置</strong>
                    </div>
                    <div class="card-body ">

                        <form action="">
                            <div class="form-check mb-4 text-start">
                                <input class="form-check-input" type="checkbox" value="{$state}"
                                       {if $state}checked="checked"{/if} name="state" id="state"/>
                                <label class="form-check-label"
                                       for="state">开启商城并将首页重定向到商城</label>
                            </div>
                            <div class="form-outline ">
                                <input type="text" name="title" id="title" class="form-control"
                                       value="{$title}"/>
                                <label class="form-label" for="title">商城名称</label>
                            </div>


                            <div class="wysiwyg mb-4" data-mdb-wysiwyg="wysiwyg"></div>

                            <button type="button" class="btn btn-primary mb-2" id="save">
                                保存
                            </button>

                        </form>
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
<script src="../../public/plugins/js/wysiwyg.min.js?v={$__version}" defer></script>
{*TODO 商城js*}
<script src="../../public/app/shop_setting.js?v={$__version}" defer></script>
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

<div class="modal fade" id="mailDetailList" tabindex="-1" aria-labelledby="addOrUpdateTitle" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addOrUpdateTitle">{lang("邮件往来")}</h5>
                <button type="button" class="btn-close" data-mdb-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="datatable_detail"
                     data-mdb-loading="true"
                     data-mdb-rows-text="{lang('每一页显示数量：')}"
                     data-mdb-no-found-message="{lang('空空如也')}"
                     data-mdb-of-text="{lang('/')}"
                     data-mdb-pagination="false"
                     data-mdb-striped="true"
                     data-mdb-clickable-rows="true"
                ></div>
                <div id="pagination_detail" style="margin: 0 auto" class="mt-3" data-previous="{lang("上一页")}"
                     data-next="{lang("下一页")}"></div>

            </div>
        </div>


    </div>


    <div class="modal fade" id="mailDetail" tabindex="-1" aria-labelledby="addOrUpdateTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="mailDetailTitle"></h5>
                    <button type="button" class="btn-close" data-mdb-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="mailDetailBody">

                </div>

            </div>
        </div>


        {include file="layout_scripts"}

        <script defer src="../../public/app/mail.js?v={$__version}"></script>

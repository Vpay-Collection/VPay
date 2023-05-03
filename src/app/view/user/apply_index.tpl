<div class="container my-5 py-5">

    <!-- Section: Design Block -->
    <section class="mb-10">


        <div class="row">

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

    <script defer src="../../public/app/apply.js?v={$__version}"></script>

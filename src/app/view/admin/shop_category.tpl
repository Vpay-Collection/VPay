<!-- Container for demo purpose -->
<div class="container my-5 py-5">

    <!-- Section: Design Block -->
    <section class="mb-10">


        <div class="row">
            <div class="col-12 mb-2">
                <div class="card">
                    <div class="card-body">
                        <button type="button" id="addApp" class="btn btn-primary" data-mdb-toggle="modal"
                                data-mdb-target="#addOrUpdate">添加分类
                        </button>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div id="datatable"
                             data-mdb-loading="true"
                             data-mdb-rows-text="每一页显示数量："
                             data-mdb-no-found-message="空空如也"
                             data-mdb-of-text="/"
                             data-mdb-pagination="false"
                             data-mdb-striped="true"
                        ></div>
                        <div id="pagination" style="margin: 0 auto" class="mt-3" data-previous="上一页"
                             data-next="下一页"></div>
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
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addOrUpdateTitle" data-add="新增分类"
                    data-change="修改分类">新增分类</h5>
                <button type="button" class="btn-close" data-mdb-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form class="form form-vertical " id="form">
                    <div class="form-outline mb-4 d-none">
                        <input type="text" class="form-control" id="form_id" name="id" placeholder="" value=""/>
                        <label class="form-label" for="form_id"></label>
                    </div>
                    <div class="form-outline mb-4">
                        <input type="text" class="form-control" id="category_name" name="category_name"/>
                        <label class="form-label" for="category_name">分类名称</label>
                    </div>


                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-mdb-dismiss="modal">关闭</button>
                <button type="button" class="btn btn-primary" id="saveOrUpdate"
                        data-mdb-dismiss="modal">保存
                </button>
            </div>
        </div>
    </div>


    {include file="layout_scripts"}
    {*TODO categoryJS*}
    <script src="../../public/app/category.js?v={$__version}" defer></script>
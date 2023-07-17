<!-- Container for demo purpose -->
<div class="container my-5 pb-5">

    <!-- Section: Design Block -->
    <section class="mb-10">

        <div class="card-body ps-2 pb-4">
            <h3 class="">应用管理</h3>
        </div>


        <div class="row">
            <div class="col-12 mb-2">
                <div class="card">
                    <div class="card-body">
                        <button type="button" id="addApp" class="btn btn-primary" data-mdb-toggle="modal"
                                data-mdb-target="#addOrUpdate">添加应用
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



<div class="modal fade" id="addOrUpdate" tabindex="-1" aria-labelledby="addOrUpdateTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addOrUpdateTitle" data-add="新增应用"
                    data-change="修改应用">新增应用</h5>
                <button type="button" class="btn-close" data-mdb-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form class="form form-vertical " id="form">
                    <div class="form-outline mb-4 d-none">
                        <input type="text" class="form-control" id="form_id" name="id" placeholder="" value=""/>
                        <label class="form-label" for="form_id"></label>
                    </div>
                    <div class="form-outline mb-4">
                        <input type="text" class="form-control" id="app_name" name="app_name"/>
                        <label class="form-label" for="app_name">网站名称</label>
                    </div>

                    <div class="file-upload-wrapper mb-4 ">
                        <input
                                id="file-upload"
                                type="file"
                                name="app_image"
                                data-mdb-file-upload="file-upload"
                                class="file-upload-input"
                                data-mdb-multiple="false"
                                data-mdb-remove-btn="删除"
                                data-mdb-accepted-extensions="image/*"
                                data-mdb-preview-msg="拖拽到此或点击这里进行上传"
                                data-mdb-default-msg="拖拽到此或点击这里进行上传"
                                data-mdb-format-error="不支持该文件 (支持的格式为 ~~~)"
                        />
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

    <script src="../../public/app/app.js?v={$__version}" defer></script>
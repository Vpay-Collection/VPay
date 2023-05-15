<!-- Container for demo purpose -->
<link rel="stylesheet" href="../../public/app/dist/ui/trumbowyg.min.css?v={$__version}" media="none"
      onload="this.media='all'">
<link rel="stylesheet" href="../../public/app/dist/plugins/colors/ui/trumbowyg.colors.min.css?v={$__version}"
      media="none"
      onload="this.media='all'">
<div class="container my-5 py-5">

    <!-- Section: Design Block -->
    <section class="mb-10">


        <div class="row">
            <div class="col-12 mb-2">
                <div class="card">
                    <div class="card-body">

                        <div class="row ">
                            <div class="col-12 col-lg-3 mb-4">
                                <button type="button" id="addApp" class="btn btn-primary" data-mdb-toggle="modal"
                                        data-mdb-target="#addOrUpdate">添加商品
                                </button>
                            </div>
                            <div class="col-12 col-lg-3 mb-4">
                                <select class="select" name="item_category" id="item_category">
                                    <option value="" selected>不限</option>
                                    {foreach $category as $item}
                                        <option value="{$item.id}">{$item.name }</option>
                                    {/foreach}
                                </select>
                                <label class="form-label select-label" for="item_category">分类</label>
                            </div>
                            <div class="col-12 col-lg-3 mb-4">
                                <div class="form-outline">
                                    <input type="text" id="form_name" class="form-control" name="name"/>
                                    <label class="form-label" for="form_name">商品名称</label>
                                </div>
                            </div>
                            <div class="col-12 col-lg-3 mb-4">
                                <button type="button" id="search" class="btn btn-primary">查找</button>
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
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addOrUpdateTitle" data-add="新增商品"
                    data-change="修改商品">新增商品</h5>
                <button type="button" class="btn-close" data-mdb-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form class="form form-vertical " id="form">
                    <div class="form-outline mb-4 d-none">
                        <input type="text" class="form-control" id="form_id" name="id" placeholder="" value=""/>
                        <label class="form-label" for="form_id"></label>
                    </div>
                    <div class="form-outline mb-4">
                        <input type="text" class="form-control" id="item_name" name="item_name"/>
                        <label class="form-label" for="item_name">商品名称</label>
                    </div>

                    <div class="file-upload-wrapper mb-4 ">
                        <input
                                id="file-upload"
                                type="file"
                                name="icon"
                                data-mdb-file-upload="file-upload"
                                class="file-upload-input"
                                data-mdb-multiple="false"
                                data-mdb-remove-btn="删除"
                                data-mdb-accepted-extensions="image/*"
                                data-mdb-preview-msg="拖拽到此或点击这里进行上传商品图片"
                                data-mdb-default-msg="拖拽到此或点击这里进行上传商品图片"
                                data-mdb-format-error="不支持该文件 (支持的格式为 ~~~)"
                        />
                    </div>
                    <div class="form-outline mb-4">
                        <input type="text" class="form-control" id="item_price" name="item_price"/>
                        <label class="form-label" for="item_price">商品价格</label>
                    </div>
                    <div class="col mb-4">
                        <select class="select" name="item_category" id="item_category">
                            {foreach $category as $item}
                                <option value="{$item.id}" {if $item@index==0}selected{/if}>{$item.name }</option>
                            {/foreach}
                        </select>
                        <label class="form-label select-label" for="item_category">商品分类</label>
                    </div>
                    <div class="form-outline mb-4">
                        <input type="text" class="form-control" id="inputs" name="inputs"/>
                        <label class="form-label" for="inputs">允许用户输入的字段名（逗号分割）</label>
                    </div>
                    <div class="form-outline mb-4">
                        <input type="text" class="form-control" id="webhook" name="webhook"/>
                        <label class="form-label" for="webhook">WebHook</label>
                    </div>
                    <div class="form-check mb-4 text-start">
                        <input class="form-check-input" type="checkbox" value="1"
                               name="auto" id="auto"/>
                        <label class="form-check-label"
                               for="auto">自动向用户邮箱发送WebHook的响应内容</label>
                    </div>
                    <div class=" mb-4 ">
                        <div id="trumbowyg">

                        </div>
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
    <script src="../../public/app/dist/trumbowyg.min.js?v={$__version}" defer></script>
    <script src="../../public/app/dist/langs/zh_cn.min.js?v={$__version}" defer></script>
    <script src="../../public/app/dist/plugins/colors/trumbowyg.colors.min.js?v={$__version}" defer></script>
    <script src="../../public/app/shop.js?v={$__version}" defer></script>
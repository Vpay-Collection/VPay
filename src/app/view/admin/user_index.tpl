<div class="container my-5 py-5">

    <!--Section: Profile-->
    <section class="mb-10">
        <div class="row">
            <div class="col-12 mb-2">
                <div class="card ">
                    <div class="card-header py-3">
                        <strong>编辑个人信息</strong>
                    </div>
                    <div class="card-body text-center">
                        <div class="mt-1 mb-4">
                            <strong>个人头像</strong>
                        </div>

                        <form action="">
                            <div class="d-flex justify-content-center mb-4">
                                <div id="dnd-default-value" class="file-upload-wrapper shadow-5"
                                     style="max-width: 300px">
                                    <input type="file" class="file-upload-input" id="image"
                                           data-mdb-default-file="{$image}"
                                           data-mdb-multiple="false"
                                           data-mdb-remove-btn="删除"
                                           data-mdb-accepted-extensions="image/*"
                                           data-mdb-preview-msg="拖拽到此或点击这里进行上传"
                                           data-mdb-default-msg="拖拽到此或点击这里进行上传"
                                           data-mdb-format-error="不支持该文件 (支持的格式为 ~~~)"
                                           data-mdb-file-upload="file-upload"/>
                                </div>
                            </div>

                            <div class="form-outline mb-4">
                                <input type="text" name="username" id="username" class="form-control"
                                       value="{$username}"/>
                                <label class="form-label" for="username">登录名</label>
                            </div>

                            <div class="form-outline mb-4">
                                <input type="password" name="old" id="old" class="form-control"
                                       value=""/>
                                <label class="form-label" for="old">原有的登录密码</label>
                            </div>
                            <div class="form-outline mb-4">
                                <input type="password" name="new" id="new" class="form-control"
                                       value=""/>
                                <label class="form-label" for="new">新的登录密码</label>
                            </div>
                            <button type="button" class="btn btn-primary mb-2" id="updateInfo">
                                更新信息
                            </button>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </section>
    <!--Section: Profile-->

</div>


{include file="layout_scripts"}
<script src="../../public/app/user.js?v={$__version}" defer></script>
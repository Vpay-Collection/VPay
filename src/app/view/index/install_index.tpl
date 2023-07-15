<!-- Section: Design Block -->
<section class=" vh-100">

    <!-- Background image -->
    <div class="p-5 text-center bg-image vh-100" style="background-image: url('../../public/img/bg.jpg');">
        <div class="mask" style="background-color: rgba(0, 0, 0, 0.55)">
            <div class="container vh-100 ">
                <div class="row d-flex justify-content-center align-items-center vh-100">
                    <div class="col-lg-10">
                        <div class="text-white">

                            <h1 class="mb-4 display-3 fw-bold ls-tight"><img
                                        src="../../public/img/profile.png"
                                        class="img-fluid rounded-pill me-3"
                                        alt="Townhouses and Skyscrapers"
                                        style="width: 60px"
                                />Vpay {$__version}</h1>
                            {if empty($functions)}
                                <button class="btn btn-outline-light btn-lg me-2" type="button" data-mdb-toggle="modal" data-mdb-target="#setting">一键安装<i
                                            class="fas fa-angle-right ms-2"></i></button>
                            {else}
                                <div class="alert" role="alert" data-mdb-color="danger">
                                    请允许PHP运行以下函数：
                                    <ul class="list-group ">
                                        {foreach $functions as $function}
                                            <li class="list-group-item" style="background: transparent">{$function}</li>
                                        {/foreach}
                                    </ul>
                                </div>
                                <h5 class="mb-5">请解决完以上冲突后重新刷新本页</h5>
                            {/if}

                            {include file="layout_footer"}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Background image -->

</section>

<div class="modal " tabindex="-1" id="setting">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <form>
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">安装配置</h5>
                <button type="button" class="btn-close" data-mdb-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">

                    {if !$isDocker}
                    <h6 class="mb-4">数据库配置</h6>
                    <div class="form-outline mb-4">
                        <input type="text" name="host" id="host" value="localhost"   class="form-control" />
                        <label class="form-label" for="host">数据库地址</label>
                    </div>
                    <div class="form-outline mb-4">
                        <input type="text" name="username" id="username" class="form-control" placeholder="docker安装留空" />
                        <label class="form-label" for="username">数据库用户名</label>
                    </div>
                    <div class="form-outline mb-4">
                        <input type="text" name="password" id="password" class="form-control" placeholder="docker安装留空" />
                        <label class="form-label" for="password">数据库密码</label>
                    </div>
                    <div class="form-outline mb-4">
                        <input type="text" name="database" id="database" class="form-control" placeholder="docker安装留空"/>
                        <label class="form-label" for="database" >数据库名</label>
                    </div>
                    <div class="form-outline mb-4">
                        <input type="number" name="port" id="port" value="3306" class="form-control" />
                        <label class="form-label" for="port">数据库端口</label>
                    </div>
                    {/if}
                    <h6 class="mb-4">网站配置</h6>
                    <div class="form-outline mb-4">
                        <input type="text" name="domain" id="domain" class="form-control" value="{$domain}" placeholder="docker安装留空" />
                        <label class="form-label" for="domain">本站域名</label>

                    </div>
                    <div class="alert" role="alert" data-mdb-color="danger">
                        一旦配置不可通过UI界面更换，后续更换域名请手动修改 ./app/config.php文件。<br>
                        如果不想限制域名可填写 0.0.0.0
                    </div>
                    <h6 class="mb-4">后台配置</h6>
                    <div class="form-outline mb-4">
                        <input type="text" name="_username" id="_username" class="form-control" />
                        <label class="form-label" for="_username">用户名</label>
                    </div>
                    <div class="form-outline mb-4">
                        <input type="text" name="_password" id="_password" class="form-control" />
                        <label class="form-label" for="_password">密码</label>
                    </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-mdb-dismiss="modal">关闭</button>
                <button type="submit" class="btn btn-primary">保存</button>
            </div>

        </div>
        </form>
    </div>
</div>



{include file="layout_scripts"}

<script src="../../public/app/install.js?v={$__version}" defer></script>


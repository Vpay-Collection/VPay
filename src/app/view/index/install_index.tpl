<header>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white fixed-top">
        <div class="container container-fluid justify-content-between align-items-center">
            <a href="/" class="text-reset">
                <div class="d-flex align-items-center mb-sm-0 mb-xm-2">

                    <div class="col-auto me-3">
                        <img src="{$image}" height="35"/>
                    </div>
                    <div class="col d-none d-lg-block me-3">
                        <h5 class="m-0">{$title}</h5>
                    </div>

                </div>
            </a>
            {* <form class="d-flex align-items-center mb-sm-0 mb-xm-2 ">
                 <input autocomplete="off" type="search" class="form-control rounded" placeholder="搜索商品"/>
                 <button type="button" class="input-group-text border-0"><i class="fas fa-search"></i></button>
             </form>*}

            <div class="d-flex align-items-center mb-sm-0 mb-xm-2 ms-auto me-0">
                <a class="btn btn-dark px-3" href="https://github.com/Vpay-Collection/VPay" role="button">
                    <i class="fab fa-github"></i>
                </a>
            </div>
        </div>
    </nav>

    <!-- Navbar -->

    <!-- Jumbotron -->
    <div style="margin-top: 58px"></div>
    <!-- Jumbotron -->
</header>
<div class="container my-5">

    <!--Section: Design Block-->
    <section class="mb-10 pt-5">
        <ul class="stepper" id="step"
            data-mdb-stepper-vertical-breakpoint="768"
            data-mdb-stepper-mobile-breakpoint="400"
        >
            <li class="stepper-step stepper-active">
                <div class="stepper-head">
                    <span class="stepper-head-icon">1</span>
                    <span class="stepper-head-text">安装协议</span>
                </div>
                <div class="stepper-content py-3">
                    <div class="row">
                        <div class="col-12">


                            <h2>背景</h2>

                            <p>用户希望获得Vpay并安装在其设备上，根据开源许可证的规定进行使用和分发。</p>

                            <h2>条款与条件</h2>

                            <ol>
                                <li>
                                    <strong>开源许可证</strong>
                                    <p>本软件根据特定的开源许可证（GPL 3.0）进行分发和使用。用户在安装和使用软件时必须遵守该开源许可证的要求，并在相应的分发中包含许可证条款和软件的源代码。</p>
                                </li>

                                <li>
                                    <strong>授权范围</strong>
                                    <p>用户获得非排他性的许可，可以在符合开源许可证的限制下安装、使用和复制软件。此许可证不授予用户对软件的所有权或知识产权的任何权利。</p>
                                </li>

                                <li>
                                    <strong>修改和派生作品</strong>
                                    <p>根据开源许可证的规定，如果用户基于软件创建了修改版或派生作品，则该修改版或派生作品必须在相同的开源许可证下发布，并遵守该许可证的要求，包括对修改版或派生作品的源代码的可获取性。</p>
                                </li>

                                <li>
                                    <strong>免责声明</strong>
                                    <p>软件按“现状”提供，开发者不对软件的适用性、完整性或准确性作任何明示或暗示的声明或担保。开发者不对软件使用中的任何直接或间接损害承担责任，包括但不限于利润损失、数据损失或业务中断。</p>
                                </li>

                                <li>
                                    <strong>终止</strong>
                                    <p>用户可以随时终止本协议，停止使用软件并将其从设备上删除。对于用户已经复制、分发或修改的软件副本，用户应继续遵守开源许可证的要求进行处理。</p>
                                </li>

                                <li>
                                    <strong>适用法律和争议解决</strong>
                                    <p>本协议受适用法律管辖。对于因本协议引起的任何争议，双方应尽力通过友好协商解决。如协商不成，用户同意将争议提交至有管辖权的法院进行解决。</p>
                                </li>

                                <li>
                                    <strong>其他条款</strong>
                                    <p>本协议构成用户与开发者之间就开源软件安装达成的完整协议，并取代双方之前的任何口头或书面约定。本协议中的任何条款无效或不可执行的，不应影响其他条款的有效性。</p>
                                </li>
                            </ol>

                            <p>用户确认已经阅读、理解并同意遵守本协议的所有条款和条件，并代表其自身签署本协议。</p>



                        </div>

                    </div>
                </div>
            </li>
            <li class="stepper-step" data-mdb-stepper-optional="true">
                <div class="stepper-head">
                    <span class="stepper-head-icon">2</span>
                    <span class="stepper-head-text">环境检查</span>
                </div>
                <div class="stepper-content py-3">
                    <h3>服务器环境</h3>
                    <table class="table">
                        <thead>
                        <tr>
                            <th scope="col">检查项</th>
                            <th scope="col">当前配置</th>
                            <th scope="col">最佳配置</th>
                        </tr>
                        </thead>
                        <tbody>
                        {foreach $envs as $env}
                            <tr class="{$env['status']?'table-success':'table-danger'}">
                                <th scope="row">{$env['name']}</th>
                                <td>{$env['current']}</td>
                                <td>{$env['pref']}</td>
                            </tr>
                        {/foreach}

                        </tbody>
                    </table>
                    <h3>服务器权限</h3>
                    <table class="table">
                        <thead>
                        <tr>
                            <th scope="col">路径</th>
                            <th scope="col">当前配置</th>
                            <th scope="col">最佳配置</th>
                        </tr>
                        </thead>
                        <tbody>
                        {foreach $dirs as $env}
                            <tr class="{$env['status']?'table-success':'table-danger'}">
                                <th scope="row">{$env['name']}</th>
                                <td>{$env['status']?'可读写':'不可读写'}</td>
                                <td>可读写</td>
                            </tr>
                        {/foreach}

                        </tbody>
                    </table>
                    <h3>PHP函数</h3>
                    <table class="table">
                        <thead>
                        <tr>
                            <th scope="col">函数</th>
                            <th scope="col">当前配置</th>
                            <th scope="col">最佳配置</th>
                        </tr>
                        </thead>
                        <tbody>
                        {foreach $functions as $env}
                            <tr class="{$env['status']?'table-success':'table-danger'}">
                                <th scope="row">{$env['name']}</th>
                                <td>{$env['status']?'可用':'不可用'}</td>
                                <td>可用</td>
                            </tr>
                        {/foreach}
                        </tbody>
                    </table>
                    <h3>PHP拓展</h3>
                    <table class="table">
                        <thead>
                        <tr>
                            <th scope="col">拓展</th>
                            <th scope="col">当前配置</th>
                            <th scope="col">最佳配置</th>
                        </tr>
                        </thead>
                        <tbody>
                        {foreach $extends as $env}
                            <tr class="{$env['status']?'table-success':'table-danger'}">
                                <th scope="row">{$env['name']}</th>
                                <td>{$env['status']?'可用':'不可用'}</td>
                                <td>可用</td>
                            </tr>
                        {/foreach}
                        </tbody>
                    </table>
                </div>
            </li>
            <li class="stepper-step">
                <div class="stepper-head">
                    <span class="stepper-head-icon">3</span>
                    <span class="stepper-head-text">安装配置</span>
                </div>
                <div class="stepper-content py-3">
                    <form>
                        <h6 class="mb-4">数据库配置</h6>

                        <div class="form-outline mb-4">
                            <input type="text" name="host" id="host" value="localhost"   class="form-control" />
                            <label class="form-label" for="host">数据库地址</label>
                        </div>
                        <div class="form-outline mb-4">
                            <input type="text" name="username" id="username" class="form-control" />
                            <label class="form-label" for="username">数据库用户名</label>
                        </div>
                        <div class="form-outline mb-4">
                            <input type="text" name="password" id="password" class="form-control" />
                            <label class="form-label" for="password">数据库密码</label>
                        </div>
                        <div class="form-outline mb-4">
                            <input type="text" name="database" id="database" class="form-control" />
                            <label class="form-label" for="database">数据库名</label>
                        </div>
                        <div class="form-outline mb-4">
                            <input type="number" name="port" id="port" value="3306" class="form-control" />
                            <label class="form-label" for="port">数据库端口</label>
                        </div>
                        <h6 class="mb-4">网站配置</h6>
                        <div class="form-outline mb-4">
                            <input type="text" name="domain" id="domain" class="form-control" value="{$domain}" />
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
                        <!-- Submit button -->
                        <button type="submit" class="btn btn-primary btn-block mb-4">保存配置</button>
                    </form>
                </div>
            </li>
        </ul>
    </section>
    <!--Section: Design Block-->

</div>

<div class="modal fade" id="success" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">安装成功</h5>
                <button type="button" class="btn-close" data-mdb-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <p>
                    您的登录账号：<b id="username_modal"></b>
                </p>
                <p>
                    您的登录密码：<b id="password_modal"></b>
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary"  data-mdb-dismiss="modal" onclick="location.href='/admin'">前往后台</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="error" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">安装失败</h5>
                <button type="button" class="btn-close" data-mdb-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body" id="errorInfo">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary"  data-mdb-dismiss="modal">好的</button>
            </div>
        </div>
    </div>
</div>


{include file="layout_scripts"}
<script src="../../public/app/form.js?v{$__version}" defer></script>
<script src="../../public/app/install.js?{$__version}" defer></script>

{include file="layout_footer"}
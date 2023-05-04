<div class=" w-100 h-100">
    <img src="../../public/img/bg.jpeg" style="display: none" rel="preload" is="image"/>
    <div class="bg-image" style="background-image: url('../../public/img/bg.jpeg');min-height: 100vh;">
        <!-- Section: Design Block -->
        <div class="mask position-static" style="background-color: rgba(0, 0, 0, 0.4);min-height: 100vh;">
            <div class="d-flex justify-content-center align-items-center" style="min-height: 100vh">
                <div class="container px-4 py-5 px-md-5 text-center text-lg-start my-5">
                    <div class="row gx-lg-5 align-items-center mb-5">
                        <div class="col-lg-6 mb-5 mb-lg-0" style="z-index: 10">
                            <h1 class="my-5 display-3 fw-bold ls-tight"
                                style="color: hsl(218, 81%, 95%)"
                            >
                                {$app['title']} <br/>
                                <span style="color: hsl(218, 81%, 75%)">{sprintf('统一认证登录')}</span>
                            </h1>
                            <p class="mb-4 opacity-70" style="color: hsl(218, 81%, 85%)">
                                如果动物光吃不胖，那它肚子里一定有了寄生虫。如果百姓勤劳而不能致富，那社会一定有了吸血鬼。
                            </p>
                        </div>

                        <div class="col-lg-6 mb-5 mb-lg-0 position-relative">


                            <div class="card ">
                                <div class="card-body px-4 py-5 px-md-5">
                                    <div class="col-12">
                                        <!-- Pills navs -->
                                        <ul
                                                class="nav nav-pills nav-justified mb-3"
                                                id="ex1"
                                                role="tablist"
                                        >
                                            <li class="nav-item" role="presentation">
                                                <a
                                                        class="nav-link active"
                                                        id="tab-mail"
                                                        data-mdb-toggle="pill"
                                                        href="#login-email"
                                                        role="tab"
                                                        aria-controls="pills-login"
                                                        aria-selected="true"
                                                ><i class="far fa-envelope h4 mb-0"></i></a>
                                            </li>
                                            <li class="nav-item" role="presentation">
                                                <a
                                                        class="nav-link"
                                                        id="tab-wechat"
                                                        data-mdb-toggle="pill"
                                                        href="#login-wechat"
                                                        role="tab"
                                                        aria-controls="pills-register"
                                                        aria-selected="false"
                                                ><i class="fas fa-qrcode h4 mb-0"></i></a
                                                >
                                            </li>
                                            <li
                                                    class="nav-item" role="presentation">
                                                <a
                                                        class="nav-link"
                                                        id="tab-finger"
                                                        data-mdb-toggle="pill"
                                                        href="#login-finger"
                                                        role="tab"
                                                        aria-controls="pills-register"
                                                        aria-selected="false"
                                                ><i class="fas fa-fingerprint h4 mb-0"></i></a
                                                >

                                            </li>
                                        </ul>
                                        <!-- Pills navs -->

                                        <!-- Pills content -->
                                        <div class="tab-content">
                                            <div
                                                    class="tab-pane fade show active"
                                                    id="login-email"
                                                    role="tabpanel"
                                                    aria-labelledby="tab-login"
                                            >
                                                <form>


                                                    <!-- Email input -->
                                                    <div class="form-outline mb-4">
                                                        <input type="email" name="email" id="email" class="form-control"
                                                               autocomplete="false"/>
                                                        <label class="form-label" for="email"
                                                        >{sprintf("邮箱")}</label
                                                        >
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-8">
                                                            <div class="form-outline mb-4">
                                                                <input
                                                                        type="number"
                                                                        id="code"
                                                                        class="form-control"
                                                                        minlength="4"
                                                                        maxlength="4"
                                                                />
                                                                <label class="form-label"
                                                                       for="code">{sprintf("邮箱验证码")}</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-4">
                                                            <button type="button" id="send_image"
                                                                    data-mdb-toggle="modal" data-mdb-target="#captcha"
                                                                    class="btn btn-primary btn-block mb-4">
                                                                {sprintf("发送")}
                                                            </button>
                                                        </div>
                                                    </div>


                                                    <!-- Password input -->


                                                    <!-- 2 column grid layout -->
                                                    <div class="row mb-4 ms-1">
                                                        {sprintf("未注册邮箱验证后自动登录")}

                                                    </div>

                                                    <!-- Submit button -->
                                                    <button type="submit" class="btn btn-primary btn-block mb-4">
                                                        {sprintf("登录")}
                                                    </button>

                                                </form>
                                            </div>
                                            <div
                                                    class="tab-pane fade"
                                                    id="login-wechat"
                                                    role="tabpanel"
                                                    aria-labelledby="tab-wechat"
                                            >
                                                <div class="row">
                                                    <div class="col-12">
                                                        <div id="image_loading" class="bg-image"
                                                             style="height: 300px;width: 300px;margin: 0 auto">

                                                            <img
                                                                    style="height: 300px;width: 300px;"
                                                                    src=""
                                                                    class="placeholder img-fluid"
                                                                    alt="wechat qr"
                                                                    id="qr_img"
                                                            />
                                                            <div id="image_mask" class="mask"
                                                                 style="background-color: rgba(0, 0, 0, 0.6);">
                                                                <div class="d-flex justify-content-center align-items-center h-100">
                                                                    <p class="text-white mb-0"
                                                                       id="image_mask_title"></p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                            <div
                                                    class="tab-pane fade"
                                                    id="login-finger"
                                                    role="tabpanel"
                                                    aria-labelledby="tab-finger"
                                            >
                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class="alert " role="alert" data-mdb-color="success"
                                                             id="support">
                                                            {sprintf("当前设备支持生物识别，请根据页面提示完成生物识别进行登录。")}
                                                        </div>
                                                        <div class="alert " role="alert" data-mdb-color="warning"
                                                             id="unSupport">
                                                            {sprintf("当前设备不支持生物识别，请更换设备后再试。")}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>

                        {include file="layout_footer"}
                    </div>
                </div>
            </div>

            <!-- Section: Design Block -->
        </div>


    </div>
    <!-- 验证码 -->
    <div class="modal fade" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" id="captcha">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="staticBackdropLabel">{sprintf("请输入验证码")}</h5>
                        <button type="button" class="btn-close" data-mdb-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-8" style=" align-self: center;">
                                <div class="form-outline">
                                    <input
                                            type="number"
                                            id="captcha_code"
                                            class="form-control"
                                            minlength="1"
                                            maxlength="2"
                                    />
                                    <label class="form-label" for="captcha_code">{sprintf("验证码")}</label>
                                </div>
                            </div>
                            <div class="col-4">
                                <img
                                        src=""
                                        class="img-fluid hover-shadow"
                                        alt="Los Angeles Skyscrapers"
                                        id="captcha_img"
                                        onclick="this.src='{url('api_index','mail','captcha')}?t='+(new Date().getTime())"
                                />
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                                data-mdb-dismiss="modal">{sprintf("关闭")}</button>
                        <button type="button" class="btn btn-primary" data-mdb-dismiss="modal"
                                id="submit_captcha">{sprintf("确认")}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!--信息提示框-->

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
    <script defer src="../../public/app/login-mail.js?v={$__version}"></script>
    <script defer src="../../public/app/login-wechat.js?v={$__version}"></script>
    <script defer src="../../public/app/login-finger.js?v={$__version}"></script>
    <script defer src="../../public/app/login.js?v={$__version}"></script>

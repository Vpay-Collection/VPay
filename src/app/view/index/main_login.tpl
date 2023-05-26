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
                                {sprintf('Vpay管理后台')} <br/>
                                <span style="color: hsl(218, 81%, 75%)">{sprintf('用户登录')}</span>
                            </h1>
                            <p class="mb-4 opacity-70" style="color: hsl(218, 81%, 85%)">
                                其实真正的送别没有长亭古道，没有劝君更进一杯酒，就是在一个和平时一样的清晨，他留在昨天了。
                            </p>
                        </div>

                        <div class="col-lg-6 mb-5 mb-lg-0 position-relative">


                            <div class="card ">
                                <div class="card-body px-4 py-5 px-md-5">
                                    <div class="col-12">


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
                                                        <input type="text" name="username" id="account"
                                                               class="form-control" autocomplete="false"/>
                                                        <label class="form-label" for="account"
                                                        >{sprintf("账号")}</label
                                                        >
                                                    </div>
                                                    <div class="form-outline mb-4">
                                                        <input
                                                                type="password"
                                                                id="password"
                                                                name="password"
                                                                class="form-control"

                                                        />
                                                        <label class="form-label"
                                                               for="password">{sprintf("密码")}</label>
                                                    </div>


                                                    <!-- Submit button -->
                                                    <button type="submit" data-mdb-toggle="modal"
                                                            data-mdb-target="#captcha"
                                                            class="btn btn-primary btn-block mb-4">
                                                        {sprintf("登录")}
                                                    </button>

                                                </form>
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
                                        onclick="this.src='/ankio/login/captcha?t='+(new Date().getTime())"
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

    <script defer src="../../public/app/encrypt.min.js?v={$__version}"></script>
    <script defer src="../../public/app/login.js?v={$__version}"></script>

<!DOCTYPE html>
<html lang="{$__lang}">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"/>
    <meta http-equiv="x-ua-compatible" content="ie=edge"/>
    <meta name="description" content="{$title}">
    <meta name="keywords" content="{$title}"/>
    <title>{$title}</title>
    {include file="layout_headers"}
</head>


<body class="overflow-hidden">


<div id="loading-main-page" style="height: 100vh; width: 100vw" class="" data-immersive-translate-effect="1">
    <div class="loading-mdb loading-spinner position-absolute" data-immersive-translate-effect="1">
        <div class="spinner-border loading-icon" role="status" id="" data-immersive-translate-effect="1"></div>
        <span class="loading-text" data-immersive-translate-effect="1"><font data-immersive-translate-effect="1">Loading...</font></span>
    </div>
    <div class="loading-backdrop position-absolute" id="" style="opacity: 0.4; background-color: rgb(0, 0, 0);"
         data-immersive-translate-effect="1"></div>
</div>


<header>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white fixed-top">
        <div class="container container-fluid justify-content-between align-items-center">
            <a href="{url('shop','main','index')}" class="text-reset">
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
                <button type="button" class="btn btn-primary me-2 {*d-none d-md-block*}" data-mdb-toggle="modal" data-mdb-target="#updateModal" id="notice">
                    公告
                </button>
              {*  <button type="button" class="btn btn-secondary me-2 d-none d-md-block">
                    订单查询
                </button>*}
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


<!--Footer-->
<!--Main Navigation-->

<!--Main layout-->
<main>
    <div class="container" id="container">
        {include file=$__template_file}
    </div>
</main>
<!--Main layout-->
<div class="modal top fade" id="updateModal" tabindex="-1" aria-labelledby="updateModal" aria-hidden="true"
     data-mdb-backdrop="true" data-mdb-keyboard="true">
    <div class="modal-dialog   modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">公告</h5>
                <button type="button" class="btn-close" data-mdb-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                {$notice nofilter}
            </div>
            <div class="modal-footer">

                <button type="button" class="btn btn-primary"
                        data-mdb-dismiss="modal">我知道了
                </button>
            </div>
        </div>
    </div>
</div>
<!--Footer-->
<footer>

</footer>
<!--Footer-->

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


</body>
{include file="layout_footer"}

</html>

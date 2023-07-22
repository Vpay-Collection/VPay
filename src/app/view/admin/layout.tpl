<!DOCTYPE html>
<html lang="{$__lang}">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"/>
    <meta http-equiv="x-ua-compatible" content="ie=edge"/>
    <meta name="description" content="VPay是一款让个人收款变得轻松的应用。利用安卓设备上报数据，快速接受付款，安全可靠。快捷、简单的个人收款解决方案，让您更便利地收款。">
    <meta name="keywords" content="Vpay,支付,免签,微信,支付宝,个人收款,免签约,V免签"/>
    <title>Vpay管理后台</title>
    <!-- Google Fonts Roboto -->
    <link rel="icon" href="../../public/img/default-monochrome.svg" type="image/x-icon"/>
    <!-- Font Awesome -->
    <link rel="preload" as="style" onload="this.rel='stylesheet'"
          href="../../public/css/fontawesome.min.css?v={$__version}"/>
    <link
            rel="preload" as="style" onload="this.rel='stylesheet'"
            href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap"
    />


    <!-- MDB ESSENTIAL -->
    {if $theme == "dark"}
        <link id="theme-link"  rel="preload"  as="style" onload="this.rel='stylesheet'" href="../../public/mdb/css/mdb.dark.min.css?v={$__version}"/>
    {else}
        <link id="theme-link" rel="preload"  as="style" onload="this.rel='stylesheet'" href="../../public/mdb/css/mdb.min.css?v={$__version}"/>
    {/if}
    <link rel="stylesheet" href="../../public/css/mdbAdmin.css"/>


</head>


<body class="bg-{$theme} bg-gradient" >

<div id="loadingOverlay">
    <figure>
        <div class="dot white"></div>
        <div class="dot"></div>
        <div class="dot"></div>
        <div class="dot"></div>
        <div class="dot"></div>
    </figure>
    <p></p>
</div>

<!-- Start your project here-->
<!--Main Navigation-->
<header>
    <!-- Sidenav -->
    <nav
            id="sidenav-1"
            class="sidenav"
            data-mdb-hidden="false"
            data-mdb-accordion="true"
    >
        <a
                class="ripple d-flex justify-content-center py-4"
                href="#!"
                data-mdb-ripple-color="primary"
        >
            <img
                    id="MDB-logo"
                    src="../../public/img/cover.png"
                    alt="MDB Logo"
                    draggable="false"
                    style="width: 100%"
            />
        </a>

        <ul class="sidenav-menu px-2">

        </ul>
    </nav>
    <!-- Sidenav -->

    <!-- Navbar -->
    <nav
            id="main-navbar"
            class="navbar navbar-expand-lg navbar-light fixed-top"
    >
        <!-- Container wrapper -->
        <div class="container-fluid">
            <!-- Toggler -->
            <button
                    data-mdb-toggle="sidenav"
                    data-mdb-target="#sidenav-1"
                    class="btn shadow-0 p-0 me-3 d-block d-xxl-none"
                    aria-controls="#sidenav-1"
                    aria-haspopup="true"
                    style="height: 18px"
            >
                <i class="fas fa-bars fa-lg"></i>
            </button>


            <!-- Right links -->
            <ul class="navbar-nav ms-auto d-flex flex-row align-items-center">

                {if $update}
                    <li class="nav-item dropdown">
                        <a
                                class="nav-link me-3 me-lg-0 dropdown-toggle hidden-arrow"
                                href="javascript:void(0)"
                                data-mdb-toggle="modal" data-mdb-target="#updateModal"
                                id="navbarDropdownMenuLink"
                                role="button"
                        >
                            <i class="fas fa-bell"></i>
                            <span class="badge rounded-pill badge-notification bg-danger"
                            >1</span
                            >
                        </a>
                    </li>
                {/if}


                <!-- Avatar -->
                <li class="nav-item dropdown">
                    <a
                            class="nav-link dropdown-toggle hidden-arrow d-flex align-items-center"
                            href="#"
                            id="navbarDropdownMenuLink"
                            role="button"
                            data-mdb-toggle="dropdown"
                            aria-expanded="false"
                    >
                        <img
                                src=""
                                class="rounded-circle me-2"
                                height="22"
                                alt="Avatar"
                                loading="lazy"
                                id="image"
                        />
                        <span id="username"></span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end"
                            aria-labelledby="navbarDropdownMenuLink">
                        <li><a class="dropdown-item" href="{url('admin','main','logout')}">退出登录</a></li>
                    </ul>
                </li>
            </ul>
        </div>
        <!-- Container wrapper -->
    </nav>
    <!-- Navbar -->

</header>

<!--Main layout-->
<main class="bg-light mb-5" style="margin-top: 58px">
    <!-- Container for demo purpose -->
    <div class="container p-2 p-xl-5" id="app">

        {include file=$__template_file}

    </div>
    <!-- Container for demo purpose -->
</main>
<!--Main layout-->
<div class="modal top fade" id="updateModal" tabindex="-1" aria-labelledby="updateModal" aria-hidden="true"
     data-mdb-backdrop="true" data-mdb-keyboard="true">
    <div class="modal-dialog   modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">新版本 {$new_version}</h5>
                <button type="button" class="btn-close" data-mdb-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                {$body nofilter}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-mdb-dismiss="modal">
                    关闭
                </button>
                <a type="button" href="{$download}" target="_blank" class="btn btn-primary"
                   data-mdb-dismiss="modal">下载</a>
            </div>
        </div>
    </div>
</div>

<!--Footer-->
<footer>

</footer>
<!--Footer-->
<!-- End your project here-->
</body>

</html>
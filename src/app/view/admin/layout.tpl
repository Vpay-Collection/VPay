<!DOCTYPE html>
<html lang="{$__lang}" style="min-height: 100vh;">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"/>
    <meta http-equiv="x-ua-compatible" content="ie=edge"/>
    <meta name="description" content="Vpay">
    <meta name="keywords" content="Vpay,免签,微信,支付宝"/>
    <title>Vpay管理</title>
    {include file="layout_headers"}
</head>


<body class="overflow-hidden bg-{$theme} bg-gradient">


<div id="loading-main-page" style="height: 100vh; width: 100vw" class="" data-immersive-translate-effect="1">
    <div class="loading-mdb loading-spinner position-absolute" data-immersive-translate-effect="1">
        <div class="spinner-border loading-icon" role="status" id="" data-immersive-translate-effect="1"></div>
        <span class="loading-text" data-immersive-translate-effect="1"><font data-immersive-translate-effect="1">Loading...</font></span>
    </div>
    <div class="loading-backdrop position-absolute" id="" style="opacity: 0.4; background-color: rgb(0, 0, 0);"
         data-immersive-translate-effect="1"></div>
</div>


<!--Main Navigation-->
<header>
    <!-- Sidenav -->
    <nav
            id="sidenav-1"
            class="sidenav "
            data-mdb-hidden="false"
            data-mdb-accordion="true"
    >

        <a
                class="ripple d-flex justify-content-center py-4"
                href="javascript:void(0)"
                data-mdb-ripple-color="primary"
        >
            <img
                    id="MDB-logo"
                    src="../../public/img/cover.png"
                    alt="MDB Logo"
                    style="width: 100%"
                    draggable="false"
            />
        </a>

        <ul class="sidenav-menu px-2">
            {foreach $nav as  $value}
                {if isset($value['type']) && $value['type']==0 }
                    <li class="sidenav-item sidenav-subheading">{$value['name']}</li>
                {else}
                    {if isset($value['children'])}
                        {$has = false}
                        {foreach $value['children'] as  $item}
                            {if $item['href']===$host}
                                {$has = true}
                                {break}
                            {/if}
                        {/foreach}
                        <li class="sidenav-item {$has?'active':''}">
                            <a href="javascript:void(0)" class="sidenav-link" {if isset($value['pjax'])}pjax=true{/if} >
                                <i class="{$value['icon']}  fa-fw me-3"></i>
                                <span>{$value['name']}</span>
                            </a>

                            <ul class="sidenav-collapse {$has?'show':''}">
                                {foreach $value['children'] as  $item}
                                    <li class="sidenav-item {$item['href']===$host?'active':''}">

                                        <a class="sidenav-link" href="{$item['href']}"
                                           {if isset($value['pjax'])}pjax=true{/if} ><i
                                                    class="{$item['icon']}  fa-fw me-3"></i> {$item['name']}</a>
                                    </li>
                                {/foreach}
                            </ul>
                        </li>
                    {else}
                        <li class="sidenav-item {$value['href']===$host?'active':''}">
                            <a href="{$value['href']}" class="sidenav-link" {if isset($value['pjax'])}pjax=true{/if} >
                                <i class="{$value['icon']}  fa-fw me-3"></i>
                                <span>{$value['name']}</span>
                            </a>
                        </li>
                    {/if}
                {/if}
            {/foreach}

        </ul>
    </nav>
    <!-- Sidenav -->

    <!-- Navbar -->

    <nav
            id="main-navbar"
            class="navbar navbar-expand-lg navbar-{$theme} bg-{$color} fixed-top"
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
            >
                <i class="fas fa-bars fa-lg"></i>
            </button>

            <!-- Right links -->
            <ul class="navbar-nav ms-auto d-flex flex-row">
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

                    <a class="nav-link dropdown-toggle hidden-arrow d-flex align-items-center" href="#"
                       id="navbarDropdownMenuLink" role="button" data-mdb-toggle="dropdown" aria-expanded="false">
                        <img src="{$image}" class="rounded-circle me-2" height="22" alt="Avatar"
                             loading="lazy"/>
                        {$username}
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdownMenuLink">

                        <li><a class="dropdown-item" href="{url('admin','main','logout')}">退出登录</a></li>
                    </ul>
                </li>
            </ul>
        </div>
        <!-- Container wrapper -->
    </nav>
    <!-- Navbar -->

    <!-- Section: Design Block -->
    <!-- Heading -->

    <!-- Section: Design Block -->
</header>
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


</html>

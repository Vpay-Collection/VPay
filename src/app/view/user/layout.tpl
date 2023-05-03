<!DOCTYPE html>
<html lang="{$__lang}">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"/>
    <meta http-equiv="x-ua-compatible" content="ie=edge"/>
    <meta name="description" content="{lang('Ankioの用户管理中心')}">
    <meta name="keywords" content="{lang('Ankio,用户')}"/>
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
                    src="../../public/img/logo.jpg"
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


                <!-- Icon dropdown -->
                <li class="nav-item dropdown">
                    <a
                            class="nav-link me-3 me-lg-0 dropdown-toggle hidden-arrow"
                            href="#"
                            id="navbarDropdown"
                            role="button"
                            data-mdb-toggle="dropdown"
                            aria-expanded="false"
                    >
                        {if $__lang == "zh-cn"}
                            <i class="flag-china flag m-0"></i>
                        {else}
                            <i class="flag-united-kingdom flag m-0"></i>
                        {/if}

                    </a>
                    <ul
                            class="dropdown-menu dropdown-menu-end"
                            aria-labelledby="navbarDropdown"
                    >
                        <li>
                            <a class="dropdown-item"
                               href="javascript:document.cookie='lang=eng; path=/; max-age=' + 365 * 24 * 60 * 60;location.reload()"
                            ><i class="flag-united-kingdom flag"></i>English
                                {if $__lang == "eng"}
                                    <i class="fa fa-check text-success ms-2"></i>
                                {/if}
                            </a>
                        </li>
                        <li>
                            <hr class="dropdown-divider"/>
                        </li>
                        <li>
                            <a class="dropdown-item"
                               href="javascript:document.cookie='lang=zh-cn; path=/; max-age=' + 365 * 24 * 60 * 60;location.reload()"
                            ><i class="flag-china flag"></i>中文
                                {if $__lang == "zh-cn"}
                                    <i class="fa fa-check text-success ms-2"></i>
                                {/if}</a>
                        </li>

                    </ul>
                </li>
                <!-- Avatar -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle hidden-arrow d-flex align-items-center" href="#"
                       id="navbarDropdownMenuLink" role="button" data-mdb-toggle="dropdown" aria-expanded="false">
                        <img src="{$user['image']}" class="rounded-circle me-2" height="22" alt="Avatar"
                             loading="lazy"/>{$user['nickname']}
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdownMenuLink">

                        <li><a class="dropdown-item" href="{url('user','main','logout')}">{lang("退出登录")}</a></li>
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

<!--Footer-->
<footer>

</footer>
<!--Footer-->


</body>


</html>

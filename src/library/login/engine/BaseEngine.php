<?php
/*
 * Copyright (c) 2023. Ankio. All Rights Reserved.
 */

namespace library\login\engine;


abstract class BaseEngine
{

    /**
     * 判断是否登录
     * @return bool
     */
    abstract function isLogin(): bool;

    /**
     * 退出登录
     * @return void
     */
    abstract function logout(): void;

    /**
     * 登录模块进行路由
     * @param $action
     * @return void
     */
    abstract function route($action): void;

    abstract function getLoginUrl();




    abstract function getUser(): array;
}
<?php
/*******************************************************************************
 * Copyright (c) 2022. CleanPHP. All Rights Reserved.
 ******************************************************************************/

namespace app\core\web;


/**
 * Class Session
 * @package app\core\web
 * Date: 2020/11/29 12:24 上午
 * Author: ankio
 * Description:Session操作类
 */
class Session
{
    private static ?Session $instance = null;


    /**
     * 获取实例
     * @return Session
     */
    public static function getInstance(): ?Session
    {
        if (is_null(self::$instance)) {
            $class = __CLASS__;
            self::$instance = new $class();
        }

        return self::$instance;
    }

    /**
     * 启动session
     * @return void
     */
    public function start()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
    }

    /**
     * 获取sessionId
     * @return string
     */
    public function Id(): string
    {
        return session_id();
    }

    /**
     * 设置session
     * @param string $name session名称
     * @param  $value
     * @param int $expire 过期时间,单位秒
     */
    public function set(string $name,  $value, int $expire = 0)
    {
        if (is_array($value) || is_object($value)) {
            $value = json_encode($value);
        }
        if ($expire != 0) {
            $expire = time() + $expire;

        }
        $_SESSION[$name] = $value;
        $_SESSION[$name . "_expire"] = $expire;
    }


    /**
     * 获取session
     * @param string $name 要获取的session名
     * @return string
     */
    public function get(string $name): ?string
    {
        if (!isset($_SESSION[$name])) {
            return null;
        }
        $value = $_SESSION[$name];
        if (!isset($_SESSION[$name . "_expire"])) {


            return $value;
        }
        $expire = $_SESSION[$name . "_expire"];

        if ($expire == 0 || $expire > time()) {
            return $value;
        }
        return null;
    }


    /**
     * 删除session
     * @param string $name 要删除的session名称
     */
    public function delete(string $name)
    {
        if (isset($_SESSION[$name])) {
            unset($_SESSION[$name]);
        }
        if (isset($_SESSION[$name . "_expire"])) {
            unset($_SESSION[$name . "_expire"]);
        }
    }


}
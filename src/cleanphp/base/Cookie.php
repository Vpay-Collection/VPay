<?php
/*
 * Copyright (c) 2023. Ankio.  由CleanPHP4强力驱动。
 */

namespace cleanphp\base;

/**
 * Class Cookie
 * @package cleanphp\web
 * Date: 2020/11/19 12:24 上午
 * Author: ankio
 * Description:Cookie操作类
 */
class Cookie
{
    private static ?Cookie $instance = null;
    private int $expire = 0;//过期时间 单位为s 默认是会话 关闭浏览器就不在存在
    private string $path = '/';//路径 默认在本目录及子目录下有效 /表示根目录下有效
    private string $domain = '';//域
    private bool $secure = false;//是否只在https协议下设置默认不是
    private bool $httponly = true;//如果为TRUE，则只能通过HTTP协议访问cookie。 这意味着脚本语言（例如JavaScript）无法访问cookie

    /**
     * 获取实例
     * @param int $expire
     * @param string $path
     * @param string $domain
     * @param bool $secure
     * @param bool $httponly
     * @return Cookie
     */
    public static function getInstance(int $expire = 0, string $path = "", string $domain = "", bool $secure = false, bool $httponly = true): Cookie
    {
        if (is_null(self::$instance)) {
            self::$instance = new Cookie();
        }
        return self::$instance->setOptions($expire, $path, $domain, $secure, $httponly);
    }

    /**
     * 设置Cookie
     * @param int $expire 过期时间 单位为s 默认是会话 关闭浏览器就不在存在
     * @param string $path 路径 默认在本目录及子目录下有效 /表示根目录下有效
     * @param string $domain 域
     * @param bool $secure 是否只在https协议下设置默认不是
     * @param bool $httponly 如果为TRUE，则只能通过HTTP协议访问cookie。 这意味着脚本语言（例如JavaScript）无法访问cookie
     * @return Cookie
     */
    private function setOptions(int $expire = 0, string $path = "", string $domain = "", bool $secure = false, bool $httponly = true): Cookie
    {
        $this->expire = $expire;
        $this->path = $path;
        $this->domain = $domain;
        $this->secure = $secure;
        $this->httponly = $httponly;
        return $this;
    }

    /**
     * 设置cookie
     * @param string $name
     * @param         $value
     */
    public function set(string $name, $value): void
    {

        if (is_array($value) || is_object($value))
            $value = json_encode($value);
        setcookie($name, $value, $this->expire, $this->path, $this->domain, $this->secure, $this->httponly);
    }


    /**
     * 获取cookie
     * @param string $name
     * @param mixed|null $default
     * @return array|mixed
     */
    public function get(string $name, mixed $default = null): mixed
    {
        if (!isset($_COOKIE[$name])) {
            return $default;
        }
        return parse_type($default, $_COOKIE[$name]);
    }


    /**
     * 删除cookie
     * @param string $name
     */
    public function delete(string $name): void
    {
        if (!isset($_COOKIE[$name])) {
            return;
        }
        $value = $_COOKIE[$name];
        setcookie($name, '', time() - 1, $this->path, $this->domain,
            $this->secure, $this->httponly);
        unset($value);
    }


    /**
     * cookie续期
     * @param int $time 续期时间，单位分钟
     */
    public function addTime(int $time = 5): void
    {
        foreach ($_COOKIE as $name => $value) {
            setcookie($name, $value, time() + $time * 60, $this->path, $this->domain, $this->secure, $this->httponly);
        }
    }
}
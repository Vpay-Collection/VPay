<?php

class User
{

//验证登录
    public function login($account, $password)
    {
        $conf = new Config();
        $u = $conf->GetData(Config::UserName);
        $p = $conf->GetData(Config::UserPassword);
        if ($u === $account && $p === md5($password)) {
            $cookie = md5($u . $p) . md5($p . date("Y/m/d")) . md5($p . $u);
            setcookie("token", $cookie, time() + 60 * 60, "/");
            return true;
        } else {
            return false;
        }
    }
//退出登录
    public function logout()
    {
        setcookie("token", "", time() - 60 * 60, "/");
        session_destroy();
        return true;
    }
//判断是否登录
    public function islogin()
    {
        $conf = new Config();
        $u = $conf->GetData(Config::UserName);
        $p = $conf->GetData(Config::UserPassword);
        $cookie = md5($u . $p) . md5($p . date("Y/m/d")) . md5($p . $u);
        return (isset($_COOKIE["token"]) ? $_COOKIE["token"] : false === $cookie) ? true : false;
    }
}
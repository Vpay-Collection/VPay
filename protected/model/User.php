<?php

class User
{


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

    public function logout()
    {
        setcookie("token", "", time() - 60 * 60, "/");
        session_destroy();
        return true;
    }

    public function islogin()
    {
        $conf = new Config();
        $u = $conf->GetData(Config::UserName);
        $p = $conf->GetData(Config::UserPassword);
        $cookie = md5($u . $p) . md5($p . date("Y/m/d")) . md5($p . $u);
        return (isset($_COOKIE["token"]) ? $_COOKIE["token"] : false === $cookie) ? true : false;
    }
}
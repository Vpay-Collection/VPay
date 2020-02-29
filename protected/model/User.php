<?php
namespace model;

class User
{

//验证登录
    public function login($account, $password)
    {
        $conf = new Config();
        $u = $conf->getData(Config::UserName);
        $p = $conf->getData(Config::UserPassword);

        //对前台的密码进行解密，此处为了防止中间人攻击
        if ($u === $account && $p ===  hash("sha256",md5($password.md5($account)))) {
            $time=time();
            $out=strtotime("+2 hour", $time);
            $cookie = hash("sha256",md5($account).date("Y/m/d").md5($p).$out);
            $conf->setData("LastLogin",$time);//最后登录时间
            $_SESSION["outtime"]=$out;
            setrawcookie("token",$cookie,time()+3600*2,'/');
            return true;
        } else {
            return false;
        }
    }

//退出登录
    public function logout()
    {
        setrawcookie("token", "",time()+3600*2,'/');
        $_SESSION["islogin"]=false;
        $_SESSION["outtime"]=0;
        session_destroy();
        return true;
    }

//判断是否登录
    public function isLogin($token)
    {
        $conf = new Config();
        $u = $conf->getData(Config::UserName);
        $p = $conf->getData(Config::UserPassword);
        $time=$conf->getData(Config::LastLogin);
        $out=strtotime("+2 hour", intval($time));
        $cookie = hash("sha256",md5($u).date("Y/m/d").md5($p).$out);
        if(isset($_SESSION["outtime"])&&intval($_SESSION["outtime"])>=time()&&isset($token)&&$token===$cookie)return true;
        else return false;
    }

}
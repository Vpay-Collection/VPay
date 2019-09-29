<?php

class User
{

//验证登录
    public function login($account, $password,$t)
    {
        if(time()-intval($t)>20)return false;//传递参数超时...
        $conf = new Config();
        $u = $conf->GetData(Config::UserName);
        $p = $conf->GetData(Config::UserPassword);
        $des=new Des();
        $password=$des->decrypt($password,$t);
        //对前台的密码进行解密，此处为了防止中间人攻击
        if ($u === $account && $p === hash("sha256",$password.$account)) {
            $time=time();
            $out=strtotime("+2 hour", $time);
            $cookie = hash("sha256",date("Y/m/d").$p.$out);
            $conf->UpdateData("LastLogin",$time);//最后登录时间
            $_SESSION["islogin"]=true;
            $_SESSION["outtime"]=$out;
            setcookie("token",$cookie);
            return true;
        } else {
            return false;
        }
    }

//退出登录
    public function logout()
    {
        setcookie("token", "");
        $_SESSION["islogin"]=false;
        $_SESSION["outtime"]=0;
        session_destroy();
        return true;
    }

//判断是否登录
    public function islogin()
    {
        $conf = new Config();
        $u = $conf->GetData(Config::UserName);
        $p = $conf->GetData(Config::UserPassword);
        $time=$conf->GetData(Config::LastLogin);
        $out=strtotime("+2 hour", $time);
        $cookie = hash("sha256",date("Y/m/d").$p.$out);

        if(isset($_SESSION["islogin"])&&$_SESSION["islogin"]&&isset($_SESSION["outtime"])&&intval($_SESSION["outtime"])>=time()&&isset($_COOKIE["token"])&&$_COOKIE["token"]===$cookie)return true;
        else return false;
    }
}
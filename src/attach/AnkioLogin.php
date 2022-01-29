<?php
/*******************************************************************************
 * Copyright (c) 2022. CleanPHP. All Rights Reserved.
 ******************************************************************************/
/**
 * Class AnkioLogin
 * Created By ankio.
 * Date : 2022/1/28
 * Time : 1:16 下午
 * Description :
 */

namespace app\attach;

use app\core\web\Response;
use app\core\web\Session;
use app\lib\HttpClient\HttpClient;

class AnkioLogin
{
    private string $site = "https://usertest.ankio.net";
    private string $id = "hZkcDetGFJhsRDjd5RWsJHsERzArM3By";
    private string $key = "GbF3726ABKReAa74kWaMJiXWAA6ZBiPBXdDrAK5ECxttNxMaynAjjRh757nDdekZ";
    private static $instance = null;
    public static function get(): AnkioLogin
    {
        if(self::$instance==null)
            self::$instance=new AnkioLogin();
        return self::$instance;
}
    //前去登录
    public function login(): string
    {
       return $this->site."/ui/#?i=".$this->id;
    }
    //兑换登录票据，或者说是登录token
    public function replaceTicket($ticket){
        $http = new HttpClient($this->site);
        $http->post("/callback/login/code",$this->sign(["refresh_token"=>$ticket]));
        $response = json_decode($http->getBody(),true);
        if($response!=null&&$response["code"]==200)return $response["data"];
        return null;
    }
    //判断当前用户是否登录
    public function isLogin(): bool
    {
        $http = new HttpClient($this->site);
        $http->post("/callback/Login/isLogin",$this->sign(["token"=>Session::getInstance()->get("token")]));
        $response = json_decode($http->getBody(),true);
        return ($response!=null&&$response["code"]==200);
    }

    private function sign(array $data): array
    {
        $s = new AlipaySign();
        $data["t"]=strval(time());
        $data["clientId"] = $this->id;
        $data["sign"] = $s->getSign($data,$this->key);
        return $data;
    }

    public function checkSign(): bool
    {
        $args=arg();
        unset($args["m"]);
        unset($args["a"]);
        unset($args["c"]);
        $sign_="";

        if(isset($args["sign"])){
            $sign_=$args["sign"];
            unset($args["sign"]);
        }
        if($sign_==="")
            return false;
        if(time() - intval($args["t"])>60*1000*5){
            return false;
        }
        $alipay=new AlipaySign();
        if(md5($alipay->getSign($args,$this->key))===md5($sign_)){
            return true;
        }
        return false;
    }

    public function logout()
    {
        $http = new HttpClient($this->site);
        $http->post("/callback/Login/logout",$this->sign(["token"=>Session::getInstance()->get("token")]));
    //    $response = json_decode($http->getBody(),true);
    }
}
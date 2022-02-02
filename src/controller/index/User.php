<?php
/*******************************************************************************
 * Copyright (c) 2022. CleanPHP. All Rights Reserved.
 ******************************************************************************/
/**
 * Class User
 * Created By ankio.
 * Date : 2022/1/28
 * Time : 6:57 下午
 * Description :
 */

namespace app\controller\index;

use app\attach\AnkioLogin;
use app\core\cache\Cache;
use app\core\config\Config;
use app\core\utils\FileUtil;
use app\core\web\Response;
use app\core\web\Session;
use app\lib\Captcha\Captcha;
use app\lib\Encryption\RSAEncryptHelper;


class User extends BaseController
{
    function init()
    {
        Session::getInstance()->start();
    }

    function isLogin(): array
    {
        if(Config::getInstance("pay")->getOne("login")=="ankio"){
            $bool = AnkioLogin::get()->isLogin();
            return $this->ret($bool,null,["url"=>AnkioLogin::get()->login()]);
        }else{
            Cache::init(3600*24);
            $token = Cache::get("token");
            $isLogin = !($token == null) && md5($token) == md5(arg("token")) && Session::getInstance()->get("img")!==null;
            return $this->ret($isLogin);
        }

    }
    /**
     * 获取加密公钥
     * @return array
     */
    function publicKey(): array
    {
        $public= APP_STORAGE."key/public.key";
        $private= APP_STORAGE."key/private.key";
        if(is_file($public)&&is_file($private)){
            return $this->ret(200,null,file_get_contents($public));
        }else{
            $rsa=new RSAEncryptHelper();
            $rsa->create();
            $keys = $rsa->getKey();
            FileUtil::mkDir(APP_STORAGE."key".DS);
            file_put_contents($public,$keys["public_key"]);
            file_put_contents($private,$keys["private_key"]);
            return $this->ret(200,"OK",$keys["public_key"]);
        }
    }
    /**
     * 登录命令
     * @return array
     */
    function login(): array
    {
        $captcha=new Captcha();
        if(!$captcha->verify(arg("code"))){
            return $this->ret(403,"验证码错误");
        }
        $private=APP_STORAGE."key/private.key";
        $public=APP_STORAGE."key/public.key";
        $rsa=new RSAEncryptHelper();
        $rsa->initRSAPath($private,$public);
        $passwd = $rsa->rsaPrivateDecrypt(arg("password"));
        $user = arg("username");
        $data =  Config::getInstance("pay")->get()["user"];
        $passwd2 = $data["password"];
        $hash1=md5($data["username"].$passwd);
        $hash2=$passwd2;
        if($hash1===$hash2 && $user===$data["username"]){
            $timeout=time()+3600*24;
            $token = sha1($hash1.md5($timeout));
            Cache::init(3600*24);
            Cache::set("token",$token);

            Session::getInstance()->set("nickName",$user);
            Session::getInstance()->set("img","./img/face.jpg");


            return $this->ret(200,"登录成功",['token'=>$token]);
        }
        return $this->ret(403,"登录失败");

    }

    /**
     * 验证码
     */
    function captcha()
    {
        $captcha = new Captcha();
        $captcha->create();
        exitApp("out put img");
    }

    function logout(): array
    {
        if(Config::getInstance("pay")->getOne("login")=="ankio"){
            Cache::init(3600*24);
            Cache::set("token","");
        }else{
            AnkioLogin::get()->logout();
        }
        return $this->ret(200);
    }

    function callback(){
        $bool = AnkioLogin::get()->checkSign();
        if(!$bool) Response::msg(true,403,"禁止登录","您的身份认证信息已过期！",10,"/");
        $data = AnkioLogin::get()->replaceTicket(arg("code"));
        if($data!=null){
            if($data["uid"]!=="1"){
                AnkioLogin::get()->logout();
                Response::msg(true,403,"禁止登录","Vpay已禁止您登录，请联系管理员。",-1);
            }else{
                Session::getInstance()->set("nickName",$data["nickName"]);
                Session::getInstance()->set("mail",$data["mail"]);
                Session::getInstance()->set("img",$data["img"]);
                Session::getInstance()->set("token",$data["access_token"]);
                Session::getInstance()->set("uid",$data["uid"]);
                Response::location("/ui/".Config::getInstance("frame")->getOne("admin")."#/");
            }
        }
        Response::msg(true,403,"禁止登录","您的身份认证信息无效。",10,"/");
    }

    public function ret($code,$msg=null,$data=null,$count=0): array
    {
        if(is_bool($code)){
            if($code){
                $code=200;
            }else{
                $code=403;
            }
        }
        if($msg==null){
            $msg="OK";
        }
        return ["code"=>$code,"msg"=>$msg,"data"=>$data,"count"=>$count];
    }
}
<?php

namespace app\controller\admin;


use app\core\cache\Cache;
use app\core\config\Config;
use app\core\debug\Log;
use app\core\release\File;
use app\core\web\Session;
use app\lib\Captcha\Captcha;
use app\lib\Encryption\RSAEncryptHelper;

class Login extends BaseController
{
    function isLogin(){
        $login=$this->logon();
        if($login!=null){
            return $login;
        }
        $pay =  Config::getInstance("pay")->get();

        return $this->ret(200,"已登录",[
            'name'=>$pay['user']['name'],
            'url'=>'/ui/img/avatar.png',
            'version' => Config::getInstance("frame")->getOne("verName")
        ]);
    }

    function passwd(){
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
       $pay =  Config::getInstance("pay")->get();
       $passwd2 = $pay['user']['passwd'];
       $hash1=md5($pay['user']['name'].$passwd);
       $hash2=$passwd2;
       if($hash1===$hash2 && $user===$pay['user']['name']){
           $timeout=time()+3600*24;
           $token=md5($hash1.$timeout);
           Session::getInstance()->set('token',$token,3600*24);
           Cache::init(3600*24);
           Cache::set("token",$token);
           return $this->ret(200,"登录成功",['token'=>$token]);
       }
        return $this->ret(403,"登录失败");
    }

    function publicKey(){
        $public=APP_STORAGE."key/public.key";
        $private=APP_STORAGE."key/private.key";
        if(is_file($public)&&is_file($private)){
            return $this->ret(200,null,file_get_contents($public));
        }else{
            $rsa=new RSAEncryptHelper();
            $rsa->create();
            $keys = $rsa->getKey();
            File::mkDir(APP_STORAGE."key".DS);
            file_put_contents($public,$keys["public_key"]);
            file_put_contents($private,$keys["private_key"]);
            return $this->ret(200,null,$keys["public_key"]);
        }
    }

    function captcha(){
        $captcha=new Captcha();
        $captcha->create();
    }


    function logout()
    {
        Session::getInstance()->set("token",null);
        return $this->ret(200);
    }


}
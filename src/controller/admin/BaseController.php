<?php
/*******************************************************************************
 * Copyright (c) 2020. CleanPHP. All Rights Reserved.
 ******************************************************************************/

namespace app\controller\admin;

use app\attach\AnkioLogin;
use app\core\cache\Cache;
use app\core\config\Config;
use app\core\mvc\Controller;
use app\core\web\Session;

class BaseController extends Controller
{

    public function init(): ?array
    {
        Session::getInstance()->start();
        if(Config::getInstance("pay")->getOne("login")=="ankio"){
        $bool = AnkioLogin::get()->isLogin();
        if(!$bool){
            return $this->ret(403,"未登录");
        }
        }else{
            Cache::init(3600*24);
            $token = Cache::get("token");
            $isLogin = !($token == null) && md5($token) == md5(arg("token"));
            if(!$isLogin){
                return $this->ret(false,"您还未登录");
            }
        }


        return null;
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

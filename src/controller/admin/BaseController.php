<?php
/*******************************************************************************
 * Copyright (c) 2020. CleanPHP. All Rights Reserved.
 ******************************************************************************/

namespace app\controller\admin;

use app\core\cache\Cache;
use app\core\mvc\Controller;
use app\core\web\Session;

class BaseController extends Controller
{

    public function init()
    {
        parent::init();
        Session::getInstance()->start();

        if(arg('c')!=="login"){
            $isLogin=$this->logon();
            if($isLogin!==null)
                return $isLogin;
        }
        return null;
    }
    public function ret($code,$msg=null,$data=null,$count=0){
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

    public function logon(){
        Cache::init(3600*24);
        $token2 = Cache::get("token");
        $token=Session::getInstance()->get("token");
        if($token===null||$token2===null||$token2!==$token||$token!==arg("token")){
            return $this->ret(403,"未登录！");
        }
        return null;
    }
}

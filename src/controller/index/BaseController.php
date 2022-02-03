<?php
/*******************************************************************************
 * Copyright (c) 2020. CleanPHP. All Rights Reserved.
 ******************************************************************************/

namespace app\controller\index;

use app\core\mvc\Controller;

class BaseController extends Controller
{

    function init()
    {

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
    //public static function err404(){}
    //public static function err500(){}
}

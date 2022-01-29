<?php

namespace app\controller\admin;

use app\core\cache\Cache;
use app\core\config\Config;

class Data extends BaseController
{
    function qr(){
        $pay =  Config::getInstance("pay")->get();
        return $this->ret(200,null,[
            "wechat_code"=> $pay["pay"]["wechat_code"],
            "alipay_code"=> $pay["pay"]["alipay_code"]
        ]);
    }

    function order(){
        $pay =  Config::getInstance("pay")->get();
        return $this->ret(200,null,[
            "validity_minute"=> $pay["pay"]["validity_minute"],
            "max_pay_numbers_in_validity_minute"=> $pay["pay"]["max_pay_numbers_in_validity_minute"],
            "pay_type"=> $pay["pay"]["pay_type"],
            "alipay_uid"=> $pay["pay"]["alipay_uid"],
            "alipay_cookie"=> $pay["pay"]["alipay_cookie"],
            "wechat_cookie"=> $pay["pay"]["wechat_cookie"]
        ]);
    }
    function app(){
        $pay =  Config::getInstance("pay")->get();
        Cache::init(3600*24);
        $last = Cache::get("last_time");
        if($last==null){
            $status="未配置";
        }else{
            if(time()-intval($last)>120){
                $status="已掉线";
            }else{
                $status="在线";
            }
        }
        return $this->ret(200,null,[
            "app_mode"=> $pay["system"]["app_mode"],
            "app_token"=> $pay["system"]["app_token"],
            "status"=> $status
        ]);
    }

    function mail(){
        $pay =  Config::getInstance("pay")->get();
        return $this->ret(200,null,[
            "smtp"=>  $pay["mail"]["smtp"],
            "send"=>$pay["mail"]["send"],
            "passwd"=>$pay["mail"]["passwd"],
            "port"=>$pay["mail"]["port"],
            "receive"=>$pay["mail"]["receive"],
            "sendType"=>$pay["mail"]["sendType"]]);
    }

}
<?php

namespace app\controller\admin;

use app\core\cache\Cache;
use app\core\config\Config;

class Data extends BaseController
{


    function order(): array
    {
        $pay =  Config::getInstance("pay")->get();
        return $this->ret(200,null,[
            "validity_minute"=> $pay["pay"]["validity_minute"],
            "alipay_private_key"=> $pay["pay"]["alipay_private_key"],
            "alipay_public_key"=> $pay["pay"]["alipay_public_key"],
            "alipay_id"=> $pay["pay"]["alipay_id"]
        ]);
    }
    function site(): array
    {
        $pay =  Config::getInstance("pay")->get();
        return $this->ret(200,null,[

            "siteName"=> $pay["pay"]["siteName"],

        ]);
    }

    function mail(): array
    {
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
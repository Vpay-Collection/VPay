<?php
namespace includes;
use lib\captcha\CaptchaBuilder;

/**
 * Captcha.php
 * User: Dreamn
 * Date: 2020/1/14 10:00
 * Description:
 */



class Captcha{


    public function Create(){

        $captch = new CaptchaBuilder();

        $captch->initialize([
            'width' => 150,     // 宽度
            'height' => 50,     // 高度
            'line' => false,     // 直线
            'curve' => true,   // 曲线
            'noise' => 1,   // 噪点背景
            'fonts' => []       // 字体
        ]);

        try {
            $re = $captch->create();

            $_SESSION['code']=strtolower($re->getText());

            $_SESSION['outtime']=strtotime('+5 Minute');//验证码5分钟有效
            $re->output();
        } catch (\Exception $e) {
        }


    }
    public function Verity($code){
        $code=strtolower($code);
        if(isset( $_SESSION['code'])&& $_SESSION['code']!==false &&isset( $_SESSION['outtime'])&&intval( $_SESSION['outtime'])>intval(time())&& $_SESSION['code']===$code){
            $_SESSION['code']=false;
            return true;
        }else{
            $_SESSION['code']=false;
            return false;
        }
    }

}
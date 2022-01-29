<?php

namespace app\controller\admin;

use app\core\cache\Cache;
use app\core\config\Config;
use app\core\web\Session;
use app\func\Email;
use app\lib\Encryption\RSAEncryptHelper;

class Setting extends BaseController
{
    function passwd(){
        $private=APP_STORAGE."key/private.key";
        $public=APP_STORAGE."key/public.key";
        $rsa=new RSAEncryptHelper();
        $rsa->initRSAPath($private,$public);
        $passwd = $rsa->rsaPrivateDecrypt(arg("password"));
        $new_password = $rsa->rsaPrivateDecrypt(arg("new_password"));
        $user = arg("username");
        $pay =  Config::getInstance("pay")->get();
        $passwd2 = $pay['user']['passwd'];
        $hash1=md5($pay['user']['name'].$passwd);
        $hash2=$passwd2;
        if($hash1===$hash2){
            $pay['user']['name']=$user;
            $pay['user']['passwd']=md5($user.$new_password);
            Config::getInstance("pay")->setAll($pay);
            Session::getInstance()->set("token",null);
            Cache::init(3600*24);
            Cache::set("token",null);
            return $this->ret(200,"修改成功，请重新登录。");
        }
        return $this->ret(403,"密码校验失败，请重新输入密码！");
    }

    function qr(){
        $pay =  Config::getInstance("pay")->get();
        if(arg("type")==="#wxup"){
            $pay["pay"]["wechat_code"]=arg("data");
        }else{
            $pay["pay"]["alipay_code"]=arg("data");
        }
        Config::getInstance("pay")->setAll($pay);
        return $this->ret(200,"二维码保存成功");
    }

    function order(){
        $pay =  Config::getInstance("pay")->get();
        $pay["pay"]["validity_minute"]=arg("validity_minute");
        $pay["pay"]["max_pay_numbers_in_validity_minute"]=arg("max_pay_numbers_in_validity_minute");
        $pay["pay"]["pay_type"]=arg("pay_type");
        $pay["pay"]["alipay_uid"]=arg("alipay_uid");
        $pay["pay"]["alipay_cookie"]=arg("alipay_cookie");
        $pay["pay"]["wechat_cookie"]=arg("wechat_cookie");
        Config::getInstance("pay")->setAll($pay);
        return $this->ret(200,"保存成功");
    }

    function app(){
        $pay =  Config::getInstance("pay")->get();
        $pay["system"]["app_mode"]=arg("app_mode");
        $pay["system"]["app_token"]=arg("app_token");
        Config::getInstance("pay")->setAll($pay);
        return $this->ret(200,"保存成功");
    }
    function mail(){
        $pay =  Config::getInstance("pay")->get();
        $pay["mail"]["smtp"]=arg("smtp");
        $pay["mail"]["send"]=arg("send");
        $pay["mail"]["passwd"]=arg("passwd");
        $pay["mail"]["port"]=arg("port");
        $pay["mail"]["receive"]=arg("receive");
        $pay["mail"]["sendType"]=arg("sendType");
        Config::getInstance("pay")->setAll($pay);
        return $this->ret(200,"保存成功");
    }
    function mail_test(){
        $mail = new Email();

        $tplData = [
            "logo" => "https://thirdwx.qlogo.cn/mmopen/vi_32/xa9aoWxWkauGPxicUx94hG87Ww7cTzcLM4icwwXUxKbnLOpPbbK8l7EuS3XRrFyaIRoZTNtValnELZibBH44Rc4gA/132",
            "sitename" => "Vpay",
            "title" => "邮件发送测试",
            "body" => "<p>您正在测试邮件发送功能</p>
                         <p><h2 style='text-align: center'>邮件测试</h2></p>
                         <p style='text-indent: 10px'>如果这不是您发出的邮件请忽略。</p>"
        ];

        $file = $mail->complie("#4076c4", "#fff", $tplData["logo"], $tplData["sitename"], $tplData["title"], $tplData["body"]);
        $pay =  Config::getInstance("pay")->get();
        ob_start();
        echo "正在尝试发送邮件>>>><br>";
        $bool = $mail->send($pay["mail"]["receive"], "【测试】{$tplData['sitename']}", $file, $tplData['sitename'],1);
        if ($bool===true) {
            echo "邮件发送成功>>>><br>";
            $data = ob_get_contents();
            ob_end_clean();
           // Cache::set($mailAddress, $mailAddress);
            return $this->ret(200,"邮件发送成功",$data);
        }else{
            echo "邮件发送失败>>>><br>".$bool;
            $data=ob_get_contents();
            ob_end_clean();
            return $this->ret(200,"邮件发送失败",$data);
        }


    }

}
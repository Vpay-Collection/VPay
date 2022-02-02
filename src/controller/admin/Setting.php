<?php

namespace app\controller\admin;

use app\attach\Email;
use app\core\cache\Cache;
use app\core\config\Config;

class Setting extends BaseController
{
    function passwd(): array
    {
        $username =  arg("username");
        $oldPasswd = arg("oldPasswd");
        $newPasswd = arg("newPasswd");
        $data =  Config::getInstance("pay")->get();
        $passwd2 = $data["user"]["password"];
        $hash1=md5($data["user"]["username"].$oldPasswd);
        $hash2=$passwd2;
        if($hash1===$hash2) {
            Cache::set("token","");
            $data["user"]["password"]=md5($username.$newPasswd);
            $data["user"]["username"]=$username;
            Config::getInstance("pay")->setAll($data);
            return $this->ret(200);
        }
        return $this->ret(403, "修改失败");
    }


    function order(){
        $pay =  Config::getInstance("pay")->get();
        $pay["pay"]["validity_minute"]=arg("validity_minute");

        $pay["pay"]["alipay_private_key"]=arg("alipay_private_key");
        $pay["pay"]["alipay_public_key"]=arg("alipay_public_key");
        $pay["pay"]["alipay_id"]=arg("alipay_id");
        Config::getInstance("pay")->setAll($pay);
        return $this->ret(200,"保存成功");
    }

    function site(){
        $pay =  Config::getInstance("pay")->get();

        $pay["pay"]["siteName"]=arg("siteName");

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
    function mail_test(): array
    {
        $mail = new Email();
        $pay =  Config::getInstance("pay")->get();
        $tplData = [
            "logo" => "http://image.ankio.net/uPic/2022_01_27_22_26_53_1643293613_1643293613307_0id1Z6.jpg",
            "sitename" => $pay["pay"]["siteName"],
            "title" => "邮件发送测试",
            "body" => "<p>您正在测试邮件发送功能</p>
                         <p><h2 style='text-align: center'>邮件测试</h2></p>
                         <p style='text-indent: 10px'>如果这不是您发出的邮件请忽略。</p>"
        ];

        $file = $mail->complieNotify("#4076c4", "#fff", $tplData["logo"], $tplData["sitename"], $tplData["title"], $tplData["body"]);
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
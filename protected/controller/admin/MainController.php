<?php

/*
 * 后台的主页面，主要用于登录检查等
 * */
namespace controller\admin;

use includes\AES;
use includes\Captcha;
use lib\speed\Speed;
use model\User;

class MainController extends BaseController
{
    // 默认首页
    public function actionIndex()
    {
        $this->display("index");

    }

    public function actionLogin()
    {//用户登录
        $userName=Speed::arg("user");
        $passWd= Speed::arg("pass");
        $captch=Speed::arg("captch");
        $capt=new Captcha();

        if(!$capt->Verity($captch))
            exit(json_encode(array("status" => false, "msg" => "验证码错误！")));

        $aes=new AES();
        $passWd=$aes->decrypt($passWd,$_SESSION['key']);


        $user = new User();


        if ($user->login($userName, $passWd)) {

            echo json_encode(array("status" => true, "msg" => "登录成功！"));
        } else {

            echo json_encode(array("status" => false, "msg" => "登录失败！"));
        }

    }

    public function actionKey(){
        $key=AES::getRandom(16);

        $_SESSION['key']=$key;
        echo json_encode(array('key'=>$key));
    }

    public function actionLogout()
    {//用户登出
        $user = new User();
        $user->logout();
        $this->jump(Speed::url('main', 'index'));
    }


}

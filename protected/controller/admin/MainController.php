<?php

/*
 * 后台的主页面，主要用于登录检查等
 * */
namespace app\controller\admin;

use app\includes\AES;
use app\includes\Captcha;
use app\lib\speed\Speed;
use app\model\Config;
use app\model\User;

class MainController extends BaseController
{

    // 默认首页
    public function actionIndex(){
        $conf = new Config();
        $u = $conf->getData(Config::UserName);
        $this->username=$u;
        $this->layout='';
    }
    public function actionWeather(){$this->layout='';}
    public function actionLogin(){$this->layout='';}

    public function actionReceive()
    {//用户登录
        $userName=arg("username");
        $passWd= arg("password");
        $captcha=arg("captcha");
        $capt=new Captcha();

        if(!$capt->Verity($captcha))
            exit(json_encode(array("code" => -1, "msg" => "验证码错误！")));

        $aes=new AES();
        $passWd=$aes->decrypt($passWd,$_SESSION['key']);


        $user = new User();


        if ($user->login($userName, $passWd)) {

            echo json_encode(array("code" => 0, "msg" => "登录成功！"));
        } else {

            echo json_encode(array("code" => -1, "msg" => "登录失败！"));
        }

    }

    public function actionKey(){
        $key=AES::getRandom(16);
        $_SESSION['key']=$key;
        echo json_encode(array('key'=>$key,'code'=>0));
    }

    public function actionLogout()
    {//用户登出
        $user = new User();
        $user->logout();
        $this->jump(url('main', 'index'));
    }
    public function actionCaptcha(){

        $c=new Captcha();

        $c->Create();
    }

    //功能页面
    public function actionConsole(){}
    public function actionSetting(){}
    public function actionMonitor(){}
    public function actionApp(){}
    public function actionAddApp(){}
    public function actionWepay(){}
    public function actionAddWepay(){}
    public function actionAlipay(){}
    public function actionAddAlipay(){}
    public function actionOrderlist(){}
    public function actionmail(){}
    public function actionGoodList(){}
    public function actionGoodEdit(){}
}

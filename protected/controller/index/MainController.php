<?php
namespace app\controller\index;


use app\model\Config;

class MainController extends BaseController
{
    // 首页
    function actionIndex(){
        $conf =new Config();
        if(intval($conf->getData(Config::Shop)))
            $this->display('shop_index');
        else $this->display('main_index');

    }


}
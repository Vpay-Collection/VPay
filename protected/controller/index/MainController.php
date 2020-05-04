<?php
namespace app\controller\index;


use app\model\Config;
use app\model\Item;

class MainController extends BaseController
{
    // 首页
    function actionIndex(){
        $conf =new Config();
        if(intval($conf->getData(Config::Shop))){
            $this->layout='';
            $item=new Item();
            $this->list=$item->get();
            $this->display('shop_index');
        }
        else $this->display('main_index');

    }


}
<?php
namespace controller\api;
/*
 * 自带的订单处理页面
 * */

class PayController extends BaseController
{


    //创建订单
    public function actionIndex()
    {

        $this->display("pay");
    }


}
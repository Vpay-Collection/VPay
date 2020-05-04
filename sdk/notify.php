<?php
/**
 * Created by dreamn.
 * Date: 2019-09-28
 * Time: 21:49
 */
include_once dirname(__FILE__).'/core/Vpay.php';

/*
 * 在这里将数据插入数据库，因为这里是后台发来的请求，可以直接信任，你也可以添加自己的方法来验证这个数据是不是来自后台，比如检查请求头啥的~
 * */
$Vpay=new Vpay();
if($Vpay->PayNotify($_GET)) {//回调时，验证通过,并且向后台发送确认消息

    //此处进行你自己的业务逻辑（数据库的插入操作）
    echo json_encode(array("state"=>Vpay::Api_Ok,"msg"=>"okok"));//由于是后端请求的，还得响应一下哈~
}else{
    //没有通过sign验证或者这笔订单异常
    echo json_encode(array("state"=>Vpay::Api_Err,"msg"=>$Vpay->getErr()));//可以通过这个查看错误信息
}
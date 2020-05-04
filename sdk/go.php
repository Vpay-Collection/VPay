<?php
/**
 * Created by dreamn.
 * Date: 2019-09-28
 * Time: 21:49
 */
session_start();
include_once dirname(__FILE__).'/pay/Vpay.php';
//封装参数
$arg["payId"]=$_GET["payId"];
$arg["price"]=$_GET["price"];
$arg["param"]=$_GET["param"];
$arg["type"]=$_GET["type"];
//此处只是演示，生产环境中不要这样写，非常不安全
/*
 * 在生产环境中，这些不应该通过url跳转实现， 而是应该在后端访问完成，然后再返回现成的数据给前端
 * */
$vpay=new Vpay();
$result=$vpay->Create($arg,$_GET["html"]);
if($result===false)echo $vpay->getErr();
else echo $result;
<?php
/**
 * Created by dreamn.
 * Date: 2019-09-28
 * Time: 19:51
 * 这个类库可以帮助你轻松地创建订单
 */

include_once dirname(__FILE__)."/lib/AlipaySign.php";
include_once dirname(__FILE__)."/lib/Web.php";
class Vpay{
    //后台api数据
    const Api_Ok=0;//接口状态ok
    const Api_Err=-1;//接口状态错误
//监控端
    const State_Online=1;//监控在线
    const State_Offline=0;//监控掉线
    const State_Nobind=-1;//监控还没绑定
//递增递减
    const PayIncrease=1;//递增
    const PayReduce=2;//递减
//订单状态常量定义
    const State_Succ = 3;//远程服务器回调成功，订单完成确认
    const State_Err = 2;//通知失败,回调服务器没有返回正确的响应信息
    const State_Ok = 1;//支付完成，通知成功
    const State_Wait = 0;//订单等待支付中
    const State_Over = -1;//订单超时
//支付选择
    const NeedHtml=1;//需要html
    const NeedData=0;//我只要支付相关的数据
//支付方式
    const PayWechat=1;
    const PayAlipay=2;
    private $conf;private $err;
    public function __construct()
    {
        $this->conf=include(dirname(__FILE__).'/config.php');
    }
    public function getErr(){
        return $this->err;
    }
    //订单创建
    public function Create($arg,$html=1){
        if(!isset($arg["payId"])){
            $this->err="请传入payId";return false;
        }
        if(!isset($arg["price"])){
            $this->err="请传入price";return false;
        }
        if(!isset($arg["param"])){
            $this->err="请传入param,不需要参数请留空~";return false;
        }
        if(!isset($arg["type"])){
            $this->err="请传入type，支付方式：1 微信 2支付宝";return false;
        }
        //进行签名
        $alipay = new AlipaySign();

        $arg["key"] = $this->conf["Key"];//把通信密钥也参与计算

        $arg["isHtml"] = $html;//采用自带的ui或者自己写ui

        $arg["appid"] = $this->conf["Appid"];//把appid也参与计算

        $sign = $alipay->getSign($arg, $this->conf["Key"]);

        $p = http_build_query($arg). '&sign=' . $sign;
        //生成签名后的url
        $web=new Web();
        $result=$web->get($this->conf["CreateOrder"]."?".$p);
        $json=json_decode($result);
        if($json->code===self::Api_Ok)
            return $json->data;
        else{
            $this->err=$json->msg;
            return false;
        }
    }
    //签名校验，此处校验的是notify或者return的签名
    private function CheckSign($arg){


        $sign = $arg['sign'];

        $arg = array_diff_key($_GET, array("sign" => $sign));

        $arg["key"] = $this->conf["Key"];//作为参与计算的主角

        $alipay = new AlipaySign();
        $_sign = $alipay->getSign($_GET, $this->conf["Key"]);

        if (md5($_sign) !== md5($_sign)) {
            $this->err="sign校验失败！";
            return false;
        }

        return true;

    }
    public function PayReturn($arg){

        return $this->CheckSign($arg);
    }//此处是同步回调
    public function PayNotify($arg){
        //检查sign
        if(!$this->CheckSign($arg))return false;
        //检查是否支付
        $payId = $arg["payId"];
        $web = new web();
        $res = $web->get($this->conf["OrderState"] . "?payId=$payId");
        $json = json_decode($res);
        if (isset($json->code) && intval($json->code) === self::Api_Ok && isset($json->state) ) {
            //这是交易完成

            if(intval($json->state) === self::State_Ok){
                $alipay=new AlipaySign();
                $key = $this->conf["Key"];
                //确认交易
                $url = $this->conf["Confirm"] . "?payId=$payId&sign=" . $alipay->getSign(array("payId" => $payId, "key" => $key), $key);
                //交易要确认
                $web->get($url);

                return true;

        }elseif($json->state === self::State_Succ){
                $this->err="该交易已经完成！";
                return false;
            }elseif($json->state === self::State_Wait){
                $this->err="正在等待交易！";
                return false;
            }elseif($json->state === self::State_Over){
                $this->err="该订单已经超时或被远程关闭！";
                return false;
            }
        }
        $this->err="订单不存在！";
        return false;

    }//此处是异步回调
    public function Close($payId){
        $web=new Web();
        $res=$web->get($this->conf["CloseOrder"]."?payId=$payId");
        $json=json_decode($res);

        if($json->code===self::Api_Err){
            $this->err=$json->msg;
            return false;
        }else return true;
    }//关闭订单，主要用于用户自己开启了之后使用
    public function getPayId(){
       $PayId = date("YmdHms") . rand(1, 9) . rand(1, 9) . rand(1, 9) . rand(1, 9);
       return $PayId;
    }
}
<?php
/*******************************************************************************
 * Copyright (c) 2022. Ankio. All Rights Reserved.
 ******************************************************************************/

/**
 * Package: Ankio
 * Class Vpay
 * Created By ankio.
 * Date : 2023/5/8
 * Time : 11:18
 * Description :
 */

namespace Ankio;

class Vpay
{
    //后台api数据
    const ApiOk = 0;//接口状态ok
    const ApiError = -1;//接口状态错误
//监控端
    const AppOnline = 1;//监控在线
    const AppOffline = 0;//监控掉线
    const AppNoBind = -1;//监控还没绑定
//递增递减
    const PayIncrease = 1;//递增
    const PayReduce = 2;//递减
//订单状态常量定义
    const StateSuccess = 3;//远程服务器回调成功，订单完成确认
    const StateError = 2;//通知失败,回调服务器没有返回正确的响应信息
    const StateOk = 1;//支付完成，通知成功
    const StateWait = 0;//订单等待支付中
    const StateOver = -1;//订单超时
//支付选择
    const NeedHtml = 1;//需要html
    const NeedData = 0;//我只要支付相关的数据
//支付方式
    const PayWechat = 1;
    const PayAlipay = 2;
    private $conf;
    private $err;

    /**
     * Pay constructor.
     * @param null $conf 配置文件数组
     */
    public function __construct($conf = null)
    {
        if ($conf)
            $this->conf = $conf;
        else
            $this->conf = include(dirname(__FILE__) . '/config.php');
        if (session_status() !== PHP_SESSION_ACTIVE)
            session_start();
    }

    /**
     * 获取错误信息
     * @return mixed
     */
    public function getErr()
    {
        return $this->err;
    }

    //订单创建
    public function Create($arg, $html = 1)
    {
        if (!isset($arg["payId"])) {
            $this->err = "请传入payId";
            return false;
        }
        if (!isset($arg["price"])) {
            $this->err = "请传入price";
            return false;
        }
        if (!isset($arg["param"])) {
            $this->err = "请传入param,不需要参数请留空~";
            return false;
        }

        if (!isset($arg["explain"])) {
            $this->err = "请传入收款原因";
            return false;
        }
        if (!isset($arg["notifyUrl"])) {
            $this->err = "请传入异步通知地址";
            return false;
        }
        if (!isset($arg["returnUrl"])) {
            $this->err = "请传入同步通知地址";
            return false;
        }
        //进行签名
        $alipay = new AlipaySign();


        $arr["payId"] = $arg["payId"];
        $arr["price"] = $arg["price"];
        $arr["param"] = $arg["param"];
        $arr["appid"] = $this->conf["Appid"];
        $arr["isHtml"] = intval($html) === 1 ? 1 : 0;
        $arr["explain"] = $arg["explain"];
        $arr["notifyUrl"] = $arg["notifyUrl"];
        $arr["returnUrl"] = $arg["returnUrl"];
        $arr["t"] = time();

        $arr["sign"] = $alipay->getSign($arr, $this->conf["Key"]);

        $arr["param"] = base64_encode(urlencode($arr["param"]));
        $arr["notifyUrl"] = urlencode($arr["notifyUrl"]);
        $arr["returnUrl"] = urlencode($arr["returnUrl"]);
        //生成签名后的url
        $_SESSION['timeOut'] = strtotime('+' . $this->conf['TimeOut'] . ' min');


        $httpClient = new HttpClient($this->conf["base"]);
        $httpClient->get($this->conf["CreateOrder"], $arr);
        $result = $httpClient->getBody();


        //  dump($arr,true);

        $json = json_decode($result);

        if ($json) {
            if ($json->code === self::ApiOk)
                return $json->data;
            else {
                $this->err = $json->msg;
                return false;
            }
        } else {
            // print_r($result);
            $this->err = '远程支付站点发生问题，或创建订单的地址有误' . $this->conf["CreateOrder"] . "<br>" . $result;
            return false;
        }

    }

    //签名校验，此处校验的是notify或者return的签名
    private function CheckSign($arg): bool
    {

        $sign = $arg['sign'];

        unset($arg['sign']);
        //       $arg = array_diff_key($_GET, array("sign" => $sign));

        $alipay = new AlipaySign();

        $_sign = $alipay->getSign($arg, $this->conf["Key"]);

        if (md5($sign) !== md5($_sign)) {
            $this->err = "sign校验失败！";
            return false;
        }

        return true;

    }

    public function PayReturn($arg): bool
    {
        $arg["param"] = base64_decode(urldecode($arg["param"]));
        $bool = $this->CheckSign($arg);
        //$payId=$this->checkClient($arg['price'],$arg['param']);
        if ($bool) {
            $this->closeClient();
            return true;
        } else {
            //   if($bool)$this->err='支付已完成！请不要重复刷新！';
            return false;
        }
    }//此处是同步回调

    public function PayNotify($arg)
    {

        //检查sign
        if (!$this->CheckSign($arg)) return false;
        //检查是否支付
        $payId = $arg["payId"];
        $httpClient = new HttpClient($this->conf["base"]);
        $httpClient->get($this->conf["OrderState"], array('payId' => $payId));
        $res = $httpClient->getBody();

        $json = json_decode($res);

        //   dump($json,true);
        if (isset($json->code) && intval($json->code) === self::ApiOk && isset($json->data->state)) {
            //这是交易完成

            if (intval($json->data->state) === self::StateOk) {
                $alipay = new AlipaySign();
                $key = $this->conf["Key"];
                //确认交易
                $param = ['payId' => $payId];
                $param['sign'] = $alipay->getSign($param, $key);
                $url = $this->conf["Confirm"];
                //交易要确认
                $httpClient = new HttpClient($this->conf["base"]);
                $httpClient->get($url, $param);
                // echo $data;
                //    dump($data,true);
                $this->err = "交易已确认！";
                return true;

            } elseif ($json->data->state === self::StateSuccess) {
                $this->err = "该交易已经完成！";
                return false;
            } elseif ($json->data->state === self::StateWait) {
                $this->err = "正在等待交易！";
                return false;
            } elseif ($json->data->state === self::StateOver) {
                $this->err = "该订单已经超时或被远程关闭！";
                return false;
            }
        }
        $this->err = "订单不存在！";
        return false;

    }//此处是异步回调

    public function Close($payId): bool
    {
        $this->closeClient();
        $httpClient = new HttpClient($this->conf["base"]);
        $alipay = new AlipaySign();
        $key = $this->conf["Key"];
        $param = ['payId' => $payId];
        $param['sign'] = $alipay->getSign($param, $key);
        $url = $this->conf["CloseOrder"];
        $httpClient->get($url, $param);
        $res = $httpClient->getBody();
        $json = json_decode($res);

        if ($json->code === self::ApiError) {
            $this->err = $json->msg;
            return false;
        } else return true;
    }//关闭订单，主要用于用户自己开启了之后使用

    public function getPayId($price, $param)
    {
        if ($PayId = $this->checkClient($price, $param)) {
            return $PayId;
        } else {
            $clientID = md5(md5($price) . sha1(urldecode($param)));
            $_SESSION['clientID'] = $clientID;
            $PayId = date("YmdHms") . rand(1, 9) . rand(1, 9) . rand(1, 9) . rand(1, 9);
            $_SESSION['payID'] = $PayId;
            return $PayId;
        }
    }

    private function checkClient($price, $param)
    {
        $param = urldecode($param);
        $clientID = md5(md5($price) . sha1($param));
        if (isset($_SESSION['clientID']) && $_SESSION['clientID'] === $clientID) {
            if (isset($_SESSION['payID']) && isset($_SESSION['timeOut']) && intval($_SESSION['timeOut']) > time()) {
                return $_SESSION['payID'];
            } else return false;
        } else return false;
    }

    private function closeClient()
    {
        $_SESSION['clientID'] = false;
        $_SESSION['timeOut'] = false;
        $_SESSION['payID'] = false;
    }
}
<?php
/*******************************************************************************
 * Copyright (c) 2022. CleanPHP. All Rights Reserved.
 ******************************************************************************/
/**
 * Class Pay
 * Created By ankio.
 * Date : 2022/1/31
 * Time : 4:24 下午
 * Description :
 */

namespace app\controller\api;

use app\attach\AlipaySign;
use app\attach\ConstData;
use app\core\config\Config;
use app\model\App;
use app\model\Order;

class Pay extends BaseController
{
    public function init()
    {
       
    }

    

    //创建订单
    public function CreateOrder(): array
    {

        $order = new Order();
        $result = $order->CreateOrder(arg());
        if (!$result){
            return $this->ret(ConstData::ApiError,$order->GetErr());
        }
        return $this->ret(ConstData::ApiOk,null,$result);


    }

    //获取订单信息
    public function GetOrder(): array
    {

        $ord = new Order();
        $res = $ord->GetOrderByOrdid(arg("orderId"));
        //所有的查询都必须加上校验功能
        $pay =  Config::getInstance("pay")->get();
        if (!empty($res)) {
            $res = $res[0];
            $time = $pay["pay"]["validity_minute"];
            $data = array(
                "payId" => $res['pay_id'],
                "orderId" => $res['order_id'],
                "img" => $res['img'],
                "price" => $res['price'],
                "reallyPrice" => $res['really_price'],
                "state" => $res['state'],
                "returnUrl" => $res['returnUrl'],
                "timeOut" => $time,
                "date" => $res['create_date'],
           //     "appid" => $res['appid'],
          //      "payUrl"=>$res["payUrl"],
          //      "isAuto"=>$res["isAuto"]
            );
            return $this->ret(ConstData::ApiOk,null,$data);
        } else {
            return $this->ret(ConstData::ApiError,"云端订单编号不存在");
        }
    }

    //查询订单状态（前端查询支付状态~）
    public function OrderState(): array
    {
        $ord = new Order();
        if(arg("payId")!==null)$res = $ord->GetOrderByPayId(arg("payId"));
        else $res = $ord->GetOrderByOrdid(arg("orderId"));
        //关闭过期订单吧..
        //$ord->closeEndOrder();
        if (!empty($res)) {
            $res = $res[0];
            $url="";
            if(intval($res["state" ])===ConstData::StateOk ||intval($res["state"])===ConstData::StateSuccess||intval($res["state"])===ConstData::StateError){
                $app=new App();
                $res2=$app->getData($res["appid"],"connect_key");
                if(empty($res2))
                    return $this->ret(ConstData::ApiError,"app不存在");
                $res2=$res2[0];
                $url=$res["returnUrl"];
                //重新签名
               // $res3 = $ord->GetOrderByOrdid(arg("orderId"));
               // dump($res,true);
                $arr["payId"]=$res["pay_id"];
                $arr["param"]=$res["param"];
                $arr["price"]=$res["price"];
                $arr["reallyPrice"]=$res["really_price"];
                $alipay=new AlipaySign();
                $arr["sign"]=$alipay->getSign($arr,$res2["connect_key"]);

                $p=http_build_query($arr);
                if(preg_match('/\?[\d\D]+/',$url)){//matched ?c
                    $url.='&'.$p;
                }else if(preg_match('/\?$/',$url)){//matched ?$
                    $url.=$p;
                }else{
                    $url.='?'.$p;
                }

            }
            return $this->ret(ConstData::ApiOk,null,["state"=>$res["state"],"data"=>$url]);
        } else {
            return $this->ret(ConstData::ApiError,"云订单编号不存在");
        }

    }

    //订单确认(远程主机发来确认请求)
    public function Confirm(): array
    {
        $payId = arg("payId");

        $ord = new Order();
        $res = $ord->GetOrderByPayId($payId);

        if (empty($res))  return $this->ret(ConstData::ApiError,"云订单编号不存在");
        $res = $res[0];
        $app = new App();

        $res2 = $app->getData($res["appid"], "connect_key");
        if(empty($res2))return $this->ret(ConstData::ApiError,"应用不存在！");
        $res2 = $res2[0];
        $key = $res2["connect_key"];

        $alipay = new AlipaySign();

        $sign = $alipay->getSign(array("payId" => $payId), $key);

        if (md5($sign) !== md5(arg("sign"))) {
            return $this->ret(ConstData::ApiError,"签名错误");
        }

        $ord->ChangeStateByPayId($payId, ConstData::StateSuccess, time());//订单确认号

        return $this->ret(ConstData::ApiOk,"成功");

        //完成确认操作
    }

    //关闭订单
    public function CloseOrder()
    {
        $payId = arg("payId");

        $ord = new Order();
        $res = $ord->GetOrderByPayId($payId);

        if (empty($res)) return $this->ret(ConstData::ApiError,"云订单编号不存在");;

        $app = new App();
        $res2 = $app->getData($res["appid"], "connect_key");
        $key = $res2["connect_key"];

        $alipay = new AlipaySign();

        $sign = $alipay->getSign(array("payId" => $payId), $key);

        if (md5($sign) !== md5(arg("sign"))) {
            return $this->ret(ConstData::ApiError,"签名错误");
        }


        //只有wait的订单允许关闭
        if ($res['state'] !== ConstData::StateWait ) {
            return $this->ret(ConstData::ApiError,"订单状态不允许关闭");
        }

        $ord->ChangeStateByPayId($payId, ConstData::StateOver, time());


        return $this->ret(ConstData::ApiOk,"成功");
    }
}
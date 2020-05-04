<?php

/*
 * Api接口
 * */
namespace app\controller\api;

use app\includes\AlipaySign;
use app\lib\phpqrcode\Code;
use app\model\App;
use app\model\Config;
use app\model\Order;
use app\model\Temp;


class ApiController extends BaseController
{


    //创建订单
    public function actionCreateOrder()
    {

        $order = new Order();
        $result = $order->CreateOrder(arg());

        if (!$result) exit(json_encode(array("code" => Config::Api_Err, "msg" => $order->GetErr())));
        if ($result["isHtml"])
        {
            $url="<html lang=''><head><title></title><meta http-equiv='refresh' content='0;url={$result["url"]}'></head><body></body></html>";
            echo json_encode(array("code" => Config::Api_Ok, "data" => $url));
        }

        else
            echo json_encode(array("code" => Config::Api_Ok, "data" => $result));

    }

    //获取订单信息
    public function actionGetOrder()
    {

        $ord = new Order();
        $res = $ord->GetOrderByOrdid(arg("orderId"));
        //所有的查询都必须加上校验功能

        if ($res) {
            $conf = new Config();
            $time = $conf->getData(Config::ValidityTime);

            $data = array(
                "payId" => $res['pay_id'],
                "orderId" => $res['order_id'],
                "payType" => $res['type'],
                "price" => $res['price'],
                "reallyPrice" => $res['really_price'],
                "state" => $res['state'],
                "timeOut" => $time,
                "date" => $res['create_date'],
                "appid" => $res['appid'],
                "payUrl"=>$res["payUrl"],
                "isAuto"=>$res["isAuto"]
            );

            echo json_encode(array("code" => Config::Api_Ok, "msg" => "获取成功！", "data" => $data));
        } else {
            echo json_encode(array("code" => Config::Api_Err, "msg" => "云端订单编号不存在"));
        }
    }

    //查询订单状态（前端查询支付状态~）
    public function actionOrderState()
    {
        $ord = new Order();
        if(arg("payId")!==null)$res = $ord->GetOrderByPayId(arg("payId"), "state,appid");
        else $res = $ord->GetOrderByOrdid(arg("orderId"), "state,appid");
        //关闭过期订单吧..
        //$ord->closeEndOrder();
        if ($res) {
            $url="";
            if(intval($res["state" ])===Order::State_Ok ||intval($res["state"])===Order::State_Succ||intval($res["state"])===Order::State_Err){
                $app=new App();
                $res2=$app->getData($res["appid"],"return_url,connect_key");
                $url=$res2["return_url"];
                //重新签名
                $res3 = $ord->GetOrderByOrdid(arg("orderId"), "pay_id,param,type,price,really_price");
                $arr["payId"]=$res3["pay_id"];
                $arr["param"]=$res3["param"];
                $arr["type"]=$res3["type"];
                $arr["price"]=$res3["price"];
                $arr["reallyPrice"]=$res3["really_price"];
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


            echo json_encode(array("code" => Config::Api_Ok,"state"=>$res["state"],"data"=>$url));
        } else {
            echo json_encode(array("code" => Config::Api_Err, "msg" => "云订单编号不存在！"));
        }

    }

    //订单确认(远程主机发来确认请求)
    public function actionConfirm()
    {
        $payId = arg("payId");

        $ord = new Order();

        $res = $ord->GetOrderByPayId($payId, "appid");

        if (!$res) exit(json_encode(array("code" => Config::Api_Err, "msg" => "云端订单号不存在！")));

        $app = new App();

        $res2 = $app->getData($res["appid"], "connect_key");
        $key = $res2["connect_key"];

        $alipay = new AlipaySign();

        $sign = $alipay->getSign(array("payId" => $payId), $key);

        if (md5($sign) !== md5(arg("sign"))) {
            exit(json_encode(array("code" => Config::Api_Err, "msg" => "签名错误！")));
        }

        $ord->ChangeStateByPayId($payId, Order::State_Succ, time());//订单确认号

        exit(json_encode(array("code" => Config::Api_Ok, "msg" => "成功！")));

        //完成确认操作
    }

    //关闭订单
    public function actionCloseOrder()
    {
        $payId = arg("payId");

        $ord = new Order();
        $res = $ord->GetOrderByPayId($payId, "appid,state,order_id");

        if (!$res) exit(json_encode(array("code" => Config::Api_Err, "msg" => "云端订单号不存在！")));

        $app = new App();
        $res2 = $app->getData($res["appid"], "connect_key");
        $key = $res2["connect_key"];

        $alipay = new AlipaySign();

        $sign = $alipay->getSign(array("payId" => $payId), $key);

        if (md5($sign) !== md5(arg("sign"))) {
            exit(json_encode(array("code" => Config::Api_Err, "msg" => "签名错误！")));
        }


        //只有wait的订单允许关闭
        if ($res['state'] !== Order::State_Wait ) {
            exit(json_encode(array("code" => Config::Api_Err, "msg" => "订单状态不允许关闭！")));
        }

        $ord->ChangeStateByPayId($payId, Order::State_Over, time());

        $tmp = new Temp();
        $tmp->DelByOid($res["order_id"]);
        exit(json_encode(array("code" => Config::Api_Ok, "msg" => "成功！")));
    }



    public function actionQr()
    {

        Code::create(arg('url'),'H',10,APP_I.'img'.DS.'qrLogo.png');

    }


}
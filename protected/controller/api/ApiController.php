<?php

/*
 * Api接口
 * */

class ApiController extends BaseController
{


    //创建订单
    public function actionCreateOrder()
    {
        $order = new Order();
        $result = $order->CreateOrder(arg());
        if (!$result) exit(json_encode(array("code" => -1, "msg" => $order->GetErr())));
        if ($result["isHtml"])
            $this->jump($result["url"]);
        else
            echo json_encode($result);


    }

    //获取订单信息
    public function actionGetOrder()
    {

        $ord = new Order();
        $res = $ord->GetOrder_id(arg("orderId"));


        if ($res) {
            $conf = new Config();
            $time = $conf->GetData(Config::close);

            $data = array(
                "payId" => $res['pay_id'],
                "orderId" => $res['order_id'],
                "payType" => $res['type'],
                "price" => $res['price'],
                "reallyPrice" => $res['really_price'],
                "payUrl" => $res['pay_url'],
                "isAuto" => $res['is_auto'],
                "state" => $res['state'],
                "timeOut" => $time,
                "date" => $res['create_date'],
                "appid" => $res['appid']
            );
            echo json_encode(array("code" => 1, "msg" => "获取成功！", "data" => $data));
        } else {
            echo json_encode(array("code" => -1, "msg" => "云端订单编号不存在"));
        }
    }

    //查询订单状态
    public function actionCheckOrder()
    {
        $ord = new Order();
        $res = $ord->GetOrder_id(arg("orderId"));

        if ($res) {
            if ($res['state'] == 0) {
                exit(json_encode(array("code" => -1, "msg" => "订单未支付")));
            }
            if ($res['state'] == -1) {
                exit(json_encode(array("code" => -2, "msg" => "订单已过期")));
            }

            $app = new App();
            $res2 = $app->getData($res["appid"], "connect_key");
            $key = $res2["connect_key"];

            $res['price'] = number_format($res['price'], 2, ".", "");
            $res['really_price'] = number_format($res['really_price'], 2, ".", "");

            $url = $res['return_url'];//返回给同步回调地址
            //取回调地址
            $arr["payId"] = $res['pay_id'];
            $arr["param"] = $res['param'];
            $arr["type"] = $res['type'];
            $arr["price"] = $res['price'];
            $arr["reallyPrice"] = $res['really_price'];
            $arr["key"] = $key;
            //参数化
            $alipay = new AlipaySign();

            $sign = $alipay->getSign($arr, $key);

            $arr["sign"] = $sign;

            $arr = array_diff_key($arr, array("key" => $key));

            $p = http_build_query($arr);//封装成url地址参数

            $url = $url . "?" . $p;

            echo json_encode(array("code" => 1, "msg" => "成功", "data" => $url));
        } else {
            echo json_encode(array("code" => -2, "msg" => "云订单编号不存在！"));
        }

    }
    //查询订单状态（简洁)）
    public function actionOrderStatus()
    {
        $ord = new Order();
        $res = $ord->GetPay_id(arg("payId"),"state");

        if ($res) {
            echo json_encode(array("code" => $res['state']));
        } else {
            echo json_encode(array("code" => -1, "msg" => "云订单编号不存在！"));
        }

    }
    //订单确认
    public function actionConfirm()
    {
        $orderId = arg("payId");

        $ord = new Order();
        $res = $ord->GetPay_id($orderId, "appid");

        if (!$res) exit(json_encode(array("code" => -1, "msg" => "云端订单号不存在！")));

        $app = new App();
        $res2 = $app->getData($res["appid"], "connect_key");
        $key = $res2["connect_key"];

        $alipay = new AlipaySign();

        $sign = $alipay->getSign(array("payId" => $orderId, "key" => $key), $key);

        if ($sign !== arg("sign")) {
            exit(json_encode(array("code" => -1, "msg" => "签名错误！")));
        }


        $ord->ChangeStatePay($orderId, 3, time());//订单确认号

        exit(json_encode(array("code" => 1, "msg" => "成功！")));

    }
    //关闭订单
    public function actionCloseOrder()
    {
        $orderId = arg("orderId");

        $ord = new Order();
        $res = $ord->GetOrder_id($orderId, "appid,state");

        if (!$res) exit(json_encode(array("code" => -1, "msg" => "云端订单号不存在！")));

        $app = new App();
        $res2 = $app->getData($res["appid"], "connect_key");
        $key = $res2["connect_key"];

        $alipay = new AlipaySign();

        $sign = $alipay->getSign(array("orderId" => $orderId, "key" => $key), $key);

        if ($sign !== arg("sign")) {
            exit(json_encode(array("code" => -1, "msg" => "签名错误！")));
        }


        if ($res['state'] !== 0) {
            exit(json_encode(array("code" => -1, "msg" => "订单状态不允许关闭！")));
        }

        $ord->ChangeState_id($orderId, -1, time());

        $tmp = new temp();
        $tmp->temp_del($orderId);
        exit(json_encode(array("code" => 1, "msg" => "成功！")));
    }

    //获取监控端状态
    public function actionGetState()
    {
        $conf = new Config();
        $key = $conf->GetData(Config::key);
        $t = arg("t");

        $_sign = $t . $key;

        if (md5($_sign) != arg("sign")) {
            exit(json_encode(array("code" => -1, "msg" => "签名错误！")));
        }


        $lastheart = $conf->GetData(Config::lastheart);

        $lastpay = $conf->GetData(Config::lastpay);

        $jkstate = $conf->GetData(Config::jkstate);


        exit(json_encode(array("code" => -1, "msg" => "成功！", "data" => array("lastheart" => $lastheart, "lastpay" => $lastpay, "jkstate" => $jkstate))));

    }


    public function actionQr()
    {

        require(APP_DIR . '/protected/lib/phpqrcode/qrlib.php');
        QRcode::png(arg("url"), false, "H", 6, 2);

    }


}
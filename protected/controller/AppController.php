<?php

/*
 * 手机app推送消息，进行心跳测试等
 * */

class AppController extends BaseController
{

    //进行心跳
    public function actionAppHeart()
    {//app心跳

        $this->closeEndOrder();

        $conf = new Config();

        $key = $conf->GetData(Config::key);//取出与app通信的密钥

        $t = arg("t");

        $_sign = $t . $key;

        if (md5($_sign) !== arg("sign")) {
            exit(json_encode(array("code" => -1, "msg" => "签名校验不通过")));
        }

        $jg = time() * 1000 - $t;
        if ($jg > 50000 || $jg < -50000) {
            exit(json_encode(array("code" => -1, "msg" => "客户端时间错误")));
        }

        $conf->UpdateData("lastheart", time());

        $conf->UpdateData("jkstate", 1);//表示正常监听

        echo json_encode(array("code" => 0, "msg" => "Success！"));
    }

    //关闭过期订单接口
    private function closeEndOrder()
    {

        $conf = new Config();

        $lastheart = $conf->GetData(Config::lastheart);//最后心跳时间

        if ((time() - $lastheart) > 60) {
            $conf->UpdateData("jkstate", 1);//表示心跳异常
        }

        $close_time = $conf->GetData(Config::close);//订单关闭的时间

        $close_time = time() - 60 * $close_time;//计算订单关闭时间

        $close_date = time();

        $ord = new Order();

        $ord->Clear_Order($close_time, $close_date);

        $rows = $ord->GetOrderClose($close_date, "order_id");//取得今天关闭的订单id

        if ($rows) {//如果有

            $tmp = new temp();

            foreach ($rows as $row) {
                $tmp->temp_del($row['order_id']);
            }

            //删除临时表中的价格信息

            $rows = $tmp->temp_getAll();
            if ($rows) {
                foreach ($rows as $row) {
                    $re = $ord->GetOrder($row["oid"], "id");//没有该记录
                    if (!$re) {
                        $tmp->temp_del($row["oid"]);
                    }
                }
            }
        }

        // echo json_encode(array("code"=>1,"msg"=>"清理完成！"));

    }

    //App数据推送
    public function actionAppPush()
    {//因为存在心跳，所以没必要每次都进行清理


        $conf = new Config();

        $key = $conf->GetData(Config::key);//取出与app通信的密钥

        $t = arg("t");

        $type = arg("type");

        $price = arg("price");

        $_sign = $type . $price . $t . $key;


        if (md5($_sign) !== arg("sign")) {
            exit(json_encode(array("code" => -1, "msg" => "签名校验不通过")));
        }

        $jg = time() * 1000 - $t;
        if ($jg > 50000 || $jg < -50000) {
            exit(json_encode(array("code" => -1, "msg" => "客户端时间错误")));
        }

        $conf->UpdateData("lastpay", time());//最后支付时间

        $ord = new Order();//对订单进行处理

        $res = $ord->GetOrderByParm($price, 0, $type);
        //TODO 在生成订单时，必须要检查一下是否当前存在正在支付的订单，存在则要求稍后再试/或者更换支付方式
        if (!$res) {
            $data = array(
                "close_date" => 0,
                "create_date" => time(),
                "is_auto" => 0,
                "notify_url" => "",
                "order_id" => "无订单转账",
                "param" => "无订单转账",
                "pay_date" => 0,
                "pay_id" => "无订单转账",
                "pay_url" => "",
                "price" => $price,
                "really_price" => $price,
                "return_url" => "",
                "state" => 1,
                "type" => $type,
                "appid" => 0
            );

            $ord->Insert($data);

            exit(json_encode(array("code" => -1, "msg" => "客户端时间错误")));

        }

        $tmp = new temp();

        $tmp->temp_del($res["order_id"]);//删除临时表

        $ord->ChangeState_id($res["order_id"], Order::OK, time(), time());//更新订单信息


        $app = new App();

        $AppRes = $app->getData($res["appid"], "connect_key,notify_url");

        $key = $AppRes['connect_key'];
        //取通讯密钥
        $url = $AppRes['notify_url'];
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


        $_GET = array_diff_key($_GET, array("key" => $key));

        $p = http_build_query($arr);//封装成url地址参数

        $url = $url . "?" . $p;

        $web = new web();

        $re = $web->get($url);

        $re = json_decode($re);

        if ($re) {
            if ($re->state) {
                echo json_encode(array("code" => 1, "msg" => $re->msg, "data" => "", "count" => "0"));
            } else {
                echo json_encode(array("code" => -3, "msg" => $re->msg, "data" => "", "count" => "0"));
            }

        } else {
            echo json_encode(array("code" => -2, "msg" => "异步回调返回的数据不是标准json数据！", "data" => "", "count" => "0"));
        }


    }


}

<?php

/*
 * 后台的api请求
 * */

class ApiController extends BaseController
{
    public function actionMainInfo()
    {

        $main = new Main();

        $todayOrder = $main->todayOrder();


        $todaySuccessOrder = $main->todaySuccessOrder();


        $todayCloseOrder = $main->todayCloseOrder();

        $todayMoney = $main->todayMoney();


        $countOrder = $main->countOrder();

        $countMoney = $main->countMoney();

        echo json_encode(array(
            "code" => 1,
            "data" => array(
                "todayOrder" => $todayOrder,
                "todaySuccessOrder" => $todaySuccessOrder,
                "todayCloseOrder" => $todayCloseOrder,
                "todayMoney" => round($todayMoney, 2),
                "countOrder" => $countOrder,
                "countMoney" => round($countMoney, 2),
            )
        ));
    }

    public function actionSettingInfo()
    {

        $conf = new Config();

        $user = $conf->GetData(Config::UserName);


        $key = $conf->GetData(Config::key);
        $close = $conf->GetData(Config::close);
        $payQf = $conf->GetData(Config::payQf);

        $wxpay = $conf->GetData(Config::wxpay);
        $zfbpay = $conf->GetData(Config::zfbpay);
        $uid = $conf->GetData(Config::uid);

        echo json_encode(array(
            "code" => 1,
            "data" => array(
                "user" => $user,
                "pass" => "",
                "key" => $key,
                "close" => $close,
                "payQf" => $payQf,
                "wxpay" => $wxpay,
                "zfbpay" => $zfbpay,
                "uid" => $uid,
            )
        ));
    }

    public function actionJkInfo()
    {

        $conf = new Config();

        $jkstate = $conf->GetData(Config::jkstate);

        $lastheart = $conf->GetData(Config::lastheart);
        $lastpay = $conf->GetData(Config::lastpay);
        $key = $conf->GetData(Config::key);

        echo json_encode(array(
            "code" => 1,
            "data" => array(
                "jkstate" => $jkstate,
                "lastheart" => $lastheart,
                "lastpay" => $lastpay,
                "key" => $key,
            )
        ));
    }

    public function actionSaveSetting()
    {
        $conf = new Config();
        $arr["user"] = arg("user");
        $arr["pass"] = arg("pass");
        $arr["uid"] = arg("uid");
        $arr["key"] = arg("key");
        $arr["wxpay"] = arg("wxpay");
        $arr["zfbpay"] = arg("zfbpay");
        $arr["close"] = arg("close");
        $arr["payQf"] = arg("payQf");
        echo $conf->UpdateDataAll($arr);
    }

    public function actionQrScan()
    {

        if (isset($_POST['base64'])) {
            $b64 = $_POST['base64'];
        } else {
            if (isset($_FILES["file"])) {
                $file = file_get_contents($_FILES["file"]["tmp_name"]);
                $b64 = base64_encode($file);
            } else {
                exit(json_encode(array("code" => 0, "msg" => "失败", "data" => "")));
            }

        }

        include_once(APP_DIR . '/protected/lib/QrCode/lib/QrReader.php');

        $qrcode = new QrReader(base64_decode($b64), QrReader::SOURCE_TYPE_BLOB);  //图片路径

        echo json_encode(array("code" => 1, "msg" => "成功", "data" => $qrcode->text()));
    }

    public function actionQr()
    {

        require(APP_DIR . '/protected/lib/phpqrcode/qrlib.php');
        QRcode::png(arg("url"), false, "H", 6, 2);

    }

    public function actionQcodeInfo()
    {
        $p = new PayCode();
        $result = $p->GetCode(arg("page"), arg("limit"), arg("type"));

        if ($result) {
            $size = sizeof($result);
            if ($size) echo json_encode(array("code" => 0, "msg" => "获取成功", "data" => $result, "count" => $size));
            else echo json_encode(array("code" => 1, "msg" => "暂无数据", "data" => $result, "count" => $size));


        } else  echo json_encode(array("code" => -1, "msg" => "暂无数据", "data" => ""));
    }

    public function actionDeleteCode()
    {
        $p = new PayCode();
        $p->DeleteCode(arg("id"));
        echo json_encode(array("code" => 1, "msg" => "删除完毕", "data" => ""));
    }

    public function actionAddQrcode()
    {
        $p = new PayCode();
        $p->CreateCode(arg("pay_url"), arg("price"), arg("type"));
        echo json_encode(array("code" => 1, "msg" => "保存完毕", "data" => ""));
    }

    public function actionOrders()
    {
        $ord = new Order();
        $res = $ord->GetOrders(arg("page"), arg("limit"), arg("type", ""), arg("state", ""));
        if ($res !== false) {
            $count = sizeof($res);
            if ($count) echo json_encode(array("code" => 0, "msg" => "获取成功", "data" => $res, "count" => $count));
            else echo json_encode(array("code" => 0, "msg" => "暂无数据", "data" => $res, "count" => $count));
        } else {
            echo json_encode(array("code" => -1, "msg" => "获取失败", "data" => "", "count" => "0"));
        }
    }

    public function actionDelOrders()
    {
        $ord = new Order();
        $ord->DelOrders(arg("id"));
        echo json_encode(array("code" => 1, "msg" => "删除成功", "data" => "", "count" => "0"));
    }

    public function actionDelGqOrder()
    {
        $ord = new Order();
        $ord->DelGqOrder();
        echo json_encode(array("code" => 1, "msg" => "删除成功", "data" => "", "count" => "0"));
    }

    public function actionDelLastOrder()
    {
        $ord = new Order();
        $ord->DelLastOrder();
        echo json_encode(array("code" => 1, "msg" => "删除成功", "data" => "", "count" => "0"));
    }

    public function actionAppcreate()
    {//添加应用
        $app = new App();

        $app->insert(arg("app_name"), arg("return_url"), arg("notify_url"), arg("connect_key"));

        echo json_encode(array("code" => 1, "msg" => "添加成功！", "data" => "", "count" => "0"));
    }

    public function actionGetApp()
    {//添加应用
        $app = new App();

        $res = $app->get(arg("page"), arg("limit"));

        if ($res) {
            $count = sizeof($res);
            if ($count === 0) echo json_encode(array("code" => 1, "msg" => "获取成功！", "data" => $res, "count" => $count));
            else  echo json_encode(array("code" => 0, "msg" => "暂无数据", "data" => $res, "count" => $count));
        } else {
            echo json_encode(array("code" => -1, "msg" => "获取失败", "data" => "", "count" => "0"));
        }


    }

    public function actionDelApp()
    {//添加应用
        $app = new App();

        $app->del(arg("id"));

        echo json_encode(array("code" => 1, "msg" => "获取成功！", "data" => "", "count" => 0));


    }

    public function actionSetBD()
    {//使用异步回调接口进行补单
        $ord = new Order();


        $res = $ord->GetOrder(arg("id"), "appid,pay_id,type,param,price,really_price,state");


        if ($res) {

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

            $arr = array_diff_key($arr, array("key" => $key));

            $p = http_build_query($arr);//封装成url地址参数

            $url = $url . "?" . $p;

            $web = new web();

            $re = $web->get($url);

            $re = json_decode($re);

            if ($re) {
                if ($re->state) {
                    $ord->ChangeState(arg("id"), Order::OK);
                    if ($res['state'] === Order::WAIT) {
                        $tmp = new temp();
                        $tmp->temp_del(arg("id"));
                    }

                    echo json_encode(array("code" => 1, "msg" => $re->msg, "data" => "", "count" => "0"));
                } else {
                    echo json_encode(array("code" => -3, "msg" => $re->msg, "data" => "", "count" => "0"));
                }

            } else {
                echo json_encode(array("code" => -2, "msg" => "异步回调返回的数据不是标准json数据！", "data" => "", "count" => "0"));
            }
        } else {
            echo json_encode(array("code" => -1, "msg" => "订单不存在", "data" => "", "count" => "0"));
        }


    }
}

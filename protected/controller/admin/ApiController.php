<?php

/*
 * 后台的api请求
 * */
namespace controller\admin;
use lib\speed\Speed;
use model\App;
use model\Config;
use model\Main;
use model\Order;
use model\PayCode;
use QRcode;
use QrReader;

class ApiController extends BaseController
{
    public function actionMainInfo()
    {

        $main = new Main();

        $todayOrder = $main->todayOrder();//获得今天订单数量
        $todaySuccessOrder = $main->todaySuccessOrder();//获得今天成功的订单
        $todayCloseOrder = $main->todayCloseOrder();//获取今天关闭的订单
        $todayMoney = $main->todayMoney();//获取今天的收入
        $countOrder = $main->countOrder();//统计总的成功订单数
        $countMoney = $main->countMoney();//统计收到的钱

        echo json_encode(array(
            "code" => Config::Api_Ok,
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
        $key = $conf->GetData(Config::Key);
        $close = $conf->GetData(Config::ValidityTime);
        $payQf = $conf->GetData(Config::Payof);

        $wxpay = $conf->GetData(Config::WechatPay);
        $zfbpay = $conf->GetData(Config::AliPay);
        $uid = $conf->GetData(Config::Ailuid);

        echo json_encode(array(
            "code" => Config::Api_Ok,
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

        $State = $conf->GetData(Config::State);

        $lastheart = $conf->GetData(Config::LastHeart);
        $lastpay = $conf->GetData(Config::LastPay);
        $key = $conf->GetData(Config::Key);




        echo json_encode(array(
            "code" => Config::Api_Ok,
            "data" => array(
                "state" => $State,
                "lastheart" => $lastheart,
                "lastpay" => $lastpay,
                "key" => $key,
            )
        ));
    }

    public function actionSaveSetting()
    {
        $conf = new Config();
        $arr["UserName"] = Speed::arg("user");
        $arr["UserPassword"] = Speed::arg("pass");
        $arr["Ailuid"] =Speed::arg("uid");
        $arr["Key"] = Speed::arg("key");
        $arr["WechatPay"] = Speed::arg("wxpay");
        $arr["AliPay"] = Speed::arg("zfbpay");
        $arr["ValidityTime"] = Speed::arg("close");
        $arr["Payof"] = Speed::arg("payQf");

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
                exit(json_encode(array("code" => Config::Api_Err, "msg" => "失败", "data" => "")));
            }

        }

        include_once(APP_DIR . '/protected/lib/QrCode/lib/QrReader.php');

        $qrcode = new QrReader(base64_decode($b64), QrReader::SOURCE_TYPE_BLOB);  //图片路径

        echo json_encode(array("code" => Config::Api_Ok, "msg" => "成功", "data" => $qrcode->text()));
    }

    public function actionQr()
    {

        require(APP_DIR . '/protected/lib/phpqrcode/qrlib.php');
        QRcode::png(Speed::arg("url"), false, "H", 6, 2);

    }

    public function actionQrInfo()
    {
        $p = new PayCode();
        $result = $p->GetCodeList(Speed::arg("page"), Speed::arg("limit"), Speed::arg("type"));

        if ($result) {
            $size = sizeof($result);
            if ($size) echo json_encode(array("code" => Config::Api_Ok, "msg" => "获取成功", "data" => $result, "count" => $size));
            else echo json_encode(array("code" => Config::Api_Err, "msg" => "暂无数据", "data" => $result, "count" => $size));


        } else  echo json_encode(array("code" => Config::Api_Err, "msg" => "暂无数据", "data" => ""));
    }

    public function actionDeleteCode()
    {
        $p = new PayCode();
        $p->DeleteCode(Speed::arg("id"));
        echo json_encode(array("code" => Config::Api_Ok, "msg" => "删除完毕", "data" => ""));
    }

    public function actionAddQr()
    {
        $p = new PayCode();
        $p->CreateCode(Speed::arg("pay_url"), Speed::arg("price"), Speed::arg("type"));
        echo json_encode(array("code" => Config::Api_Ok, "msg" => "保存完毕", "data" => ""));
    }

    public function actionOrders()
    {
        $ord = new Order();
        $res = $ord->GetOrders(Speed::arg("page"), Speed::arg("limit"), Speed::arg("type", ""), Speed::arg("state", ""));
        if ($res !== false) {
            $count = sizeof($res);
            if ($count) echo json_encode(array("code" => Config::Api_Ok, "msg" => "获取成功", "data" => $res, "count" => $count));
            else echo json_encode(array("code" => Config::Api_Ok, "msg" => "暂无数据", "data" => $res, "count" => $count));
        } else {
            echo json_encode(array("code" => Config::Api_Err, "msg" => "获取失败", "data" => "", "count" => "0"));
        }
    }

    public function actionDelOrders()
    {
        $ord = new Order();
        $ord->DelOrderById(Speed::arg("id"));
        echo json_encode(array("code" => Config::Api_Ok, "msg" => "删除成功", "data" => "", "count" => "0"));
    }

    public function actionDelGqOrder()
    {
        $ord = new Order();
        $ord->DelOverOrder();
        echo json_encode(array("code" =>Config::Api_Ok, "msg" => "删除成功", "data" => "", "count" => "0"));
    }

    public function actionDelLastOrder()
    {
        $ord = new Order();
        $ord->DelLastOrder();
        echo json_encode(array("code" => Config::Api_Ok, "msg" => "删除成功", "data" => "", "count" => "0"));
    }

    public function actionAppcreate()
    {//添加应用
        $app = new App();

        $app->insertApp(Speed::arg("app_name"), Speed::arg("return_url"), Speed::arg("notify_url"), Speed::arg("connect_key"));

        echo json_encode(array("code" => Config::Api_Ok, "msg" => "添加成功！"));
    }

    public function actionGetApp()
    {//添加应用
        $app = new App();

        $res = $app->getList(Speed::arg("page"), Speed::arg("limit"));

        if ($res) {
            $count = sizeof($res);
            if ($count === 0) echo json_encode(array("code" => Config::Api_Err, "msg" => "暂无数据", "data" => $res, "count" => $count));
            else  echo json_encode(array("code" => Config::Api_Ok, "msg" => "获取成功", "data" => $res, "count" => $count));
        } else {
            echo json_encode(array("code" => Config::Api_Err, "msg" => "没有任何数据", "data" => "", "count" => "0"));
        }


    }

    public function actionDelApp()
    {//添加应用
        $app = new App();

        $app->del(Speed::arg("id"));

        echo json_encode(array("code" => Config::Api_Ok, "msg" => "删除成功！", "data" => "", "count" => 0));


    }

    public function actionSetBD()
    {//使用异步回调接口进行补单
        $ord = new Order();

        echo $ord->Notify(Speed::arg("id"));


    }
}

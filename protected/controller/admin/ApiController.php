<?php

/*
 * 后台的api请求
 * */
namespace app\controller\admin;
use app\includes\Update;
use app\lib\speed\Speed;
use app\model\App;
use app\model\Config;
use app\model\Main;
use app\model\Order;
use app\model\PayCode;
use MGQrCodeReader\MGQrCodeReader;


class ApiController extends BaseController
{
    public function actionNav(){

        echo json_encode(array(
            array(
                "title"=>"控制台",
                "href"=>url('admin/main','console'),
                "fontFamily"=> "ok-icon",
                "icon"=> "&#xe654;",
                "spread"=>true,
                "isCheck"=> true
            ),
            array(
                "title"=>"系统设置",
                "href"=>url('admin/main','setting'),
                "fontFamily"=> "ok-icon",
                "icon"=> "&#xe68a;",
                "spread"=>true,
                "isCheck"=> false
            ),
            array(
                "title"=>"邮件设置",
                "href"=>url('admin/main','mail'),
                "fontFamily"=> "ok-icon",
                "icon"=> "&#xe7bd;",
                "spread"=>true,
                "isCheck"=> false
            ),
            array(
                "title"=>"监控端配置",
                "href"=>url('admin/main','monitor'),
                "fontFamily"=> "ok-icon",
                "icon"=> "&#xe781;",
                "spread"=>true,
                "isCheck"=> false
            ),
            array(
                "title"=>"应用管理",
                "href"=>url('admin/main','app'),
                "fontFamily"=> "ok-icon",
                "icon"=> "&#xe729;",
                "spread"=>true,
                "isCheck"=> false
            ),
            array(
                "title"=>"微信二维码",
                "href"=>url('admin/main','wepay'),
                "fontFamily"=> "ok-icon",
                "icon"=> "&#xe70c;",
                "spread"=>true,
                "isCheck"=> false
            ),
            array(
                "title"=>"支付宝二维码",
                "href"=>url('admin/main','alipay'),
                "fontFamily"=> "ok-icon",
                "icon"=> "&#xe61a;",
                "spread"=>true,
                "isCheck"=> false
            ),
            array(
                "title"=>"订单列表",
                "href"=>url('admin/main','orderlist'),
                "fontFamily"=> "ok-icon",
                "icon"=> "&#xe7d1;",
                "spread"=>true,
                "isCheck"=> false
            ),
        ));
    }
    public function actionUpdate(){
        $update=new Update($this->version);
        echo json_encode(array(
            "update"=>$update->boolUpdate(),
            "log"=>$update->getReason(),
            "url"=>$update->getUrl(),
            "lastest"=>$update->getLastest(),
            "ver"=>$update->getVer(),
        ));
    }
    public function actionConsole()
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

        $user = $conf->getData(Config::UserName);
        $key = $conf->getData(Config::Key);
        $close = $conf->getData(Config::ValidityTime);
        $payQf = $conf->getData(Config::Payof);

        $wxpay = $conf->getData(Config::WechatPay);
        $zfbpay = $conf->getData(Config::AliPay);
        $uid = $conf->getData(Config::Ailuid);
        $shop = $conf->getData(Config::Shop);

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
                "shop" => $shop,
            )
        ));
    }

    public function actionJkInfo()
    {

        $conf = new Config();

        $State = $conf->getData(Config::State);

        $lastheart = $conf->getData(Config::LastHeart);
        $lastpay = $conf->getData(Config::LastPay);
        $key = $conf->getData(Config::Key);




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
        $arr["UserName"] = arg("user");
        $arr["UserPassword"] = arg("pass");
        $arr["Ailuid"] =arg("uid");
        $arr["Key"] = arg("key");
        $arr["WechatPay"] = arg("wxpay");
        $arr["AliPay"] = arg("zfbpay");
        $arr["ValidityTime"] = arg("close");
        $arr["Payof"] = arg("payQf");
        $arr["UseShop"] = arg("shop");

        echo $conf->setDataAll($arr);
    }

    public function actionQr()
    {

        if (isset($_FILES["file"])) {
            $local=$_FILES["file"]["tmp_name"];
        } else {
            exit(json_encode(array("code" => Config::Api_Err, "msg" => "图片上传失败", "data" => "")));
        }

        $qr=new  MGQrCodeReader();
        try{
            $data=$qr->read($local);
        }catch(\Exception $e){
            exit(json_encode(array("code" => Config::Api_Err, "msg" => "二维码解码失败", "data" => "")));
        }

        echo json_encode(array("code" => Config::Api_Ok, "msg" => "成功", "data" =>$data));
    }

    public function actionLogo(){
        if (isset($_FILES["file"])) {
            $local=$_FILES["file"]["tmp_name"];
        } else {
            exit(json_encode(array("code" => Config::Api_Err, "msg" => "图片上传失败", "data" => "")));
        }
        file_put_contents(APP_I.'img'.DS.'qrLogo.png',file_get_contents($local));
        echo json_encode(array("code" => Config::Api_Ok, "msg" => "上传成功", "data" =>''));
    }
    

    public function actionQrInfo()
    {
        $p = new PayCode();
        $result = $p->GetCodeList(arg("page"), arg("limit"), arg("type"));

        if ($result) {
            $size = sizeof($result);
            if ($size) echo json_encode(array("code" => Config::Api_Ok, "msg" => "获取成功", "data" => $result, "count" => ($p->page===null)?$size:$p->page["total_count"]));
            else echo json_encode(array("code" => Config::Api_Err, "msg" => "暂无数据", "data" => $result, "count" => $size));


        } else  echo json_encode(array("code" => Config::Api_Err, "msg" => "暂无数据", "data" => ""));
    }

    public function actionDeleteCode()
    {
        $p = new PayCode();
        $p->delCode(arg("id"));
        echo json_encode(array("code" => Config::Api_Ok, "msg" => "删除完毕", "data" => ""));
    }

    public function actionAddQr()
    {
        $p = new PayCode();
        $p->addCode(arg("pay_url"), arg("price"), arg("type"));
        echo json_encode(array("code" => Config::Api_Ok, "msg" => "保存完毕", "data" => ""));
    }

    public function actionOrders()
    {
        $ord = new Order();
        $res = $ord->GetOrders(arg("page"), arg("limit"), arg("type", ""), arg("state", ""));
        if ($res !== false) {
            $count = sizeof($res);
            if ($count) echo json_encode(array("code" => Config::Api_Ok, "msg" => "获取成功", "data" => $res, "count" => ($ord->page===null)?$count:$ord->page["total_count"]));
            else echo json_encode(array("code" => Config::Api_Ok, "msg" => "暂无数据", "data" => $res, "count" => 0));
        } else {
            echo json_encode(array("code" => Config::Api_Err, "msg" => "获取失败", "data" => "", "count" => "0"));
        }
    }

    public function actionDelOrders()
    {
        $ord = new Order();
        $ord->DelOrderById(arg("id"));
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

        $app->add(arg("app_name"), arg("return_url"), arg("notify_url"), arg("connect_key"));

        echo json_encode(array("code" => Config::Api_Ok, "msg" => "添加成功！"));
    }

    public function actionGetApp()
    {//添加应用
        $app = new App();

        $res = $app->getList(arg("page"), arg("limit"));

        if ($res) {
            $count = sizeof($res);
            if ($count === 0) echo json_encode(array("code" => Config::Api_Err, "msg" => "暂无数据", "data" => $res, "count" => $count));
            else  echo json_encode(array("code" => Config::Api_Ok, "msg" => "获取成功", "data" => $res, "count"=>($app->page===null)?$count:$app->page["total_count"]));
        } else {
            echo json_encode(array("code" => Config::Api_Err, "msg" => "没有任何数据", "data" => "", "count" => "0"));
        }


    }

    public function actionDelApp()
    {//添加应用
        $app = new App();

        $app->del(arg("id"));

        echo json_encode(array("code" => Config::Api_Ok, "msg" => "删除成功！", "data" => "", "count" => 0));


    }

    public function actionSetBD()
    {//使用异步回调接口进行补单
        $ord = new Order();

        echo $ord->Notify(arg("id"));


    }
}

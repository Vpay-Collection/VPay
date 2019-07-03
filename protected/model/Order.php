<?php

/*订单处理模块
 * */

class Order extends Model
{
    //订单状态常量定义
    const ERROR = 2;//通知失败,回调服务器没有返回正确的响应信息
    const OK = 1;//支付完成，通知成功
    const WAIT = 0;//订单等待支付中
    const OVER = -1;//订单超时

    //订单构成元素
    private $payId;//远程服务器的订单号
    private $type;//支付方式
    private $price;//实际支付的价格
    private $sign;//签名
    private $isHtml;//是否跳转本地支付接口（1）（不需要额外写界面），（为0返回创建的数据包）。
    private $param;//附加参数
    private $payUrl;//支付二维码的URL
    private $appid;//订单由哪个应用创建，标记创建应用，有时候多项目用得到
    private $connect_key;//与该应用关联的通讯密钥
    private $isAuto = 1;//判断这个二维码从哪来

    private $orderId;
    private $reallyPrice;
    //模块变量
    private $err;//订单产生的错误均放在这里

    //指定表
    public function __construct($table_name = "pay_order")
    {
        parent::__construct($table_name);
    }

    //后台响应，获取所有的订单
    public function GetOrders($page, $limit, $type = "", $state = "")
    {
        $conditon = NULL;
        if ($type !== "") $conditon["type"] = $type;
        if ($state !== "") $conditon["state"] = $state;

        return $this->findAll($conditon, "id desc", "*", array($page, $limit));
    }

    //根据id删除订单，并同时删除tmp表格中的信息
    public function DelOrders($id)
    {
        $res = $this->find(array("id" => $id));
        if ($res) {
            $this->delete(array("id" => $id));
            if ($res['state'] === 0) {
                $this->reset("tmp_price");
                $this->delete(array("oid" => $res['order_id']));
            }

        }

    }

    //删除标记过期的订单
    public function DelGqOrder()
    {

        $this->delete(array("state" => -1));

    }

    //删除检查超过7天的订单
    public function DelLastOrder()
    {

        $this->delete(array("create_date <:create_date", ":create_date" => time() - 604800));

    }

    //根据id取得指定订单信息

    public function GetOrder_id($id, $parm = "*")
    {

        return $this->find(array("order_id" => $id), "", $parm);

    }

    //根据orderid取得指定订单信息

    public function ChangeState($id, $state, $paytime = "", $closetime = "")
    {
        $arr["state"] = $state;
        $arr["pay_date"] = $state;
        if ($paytime !== "") $arr["pay_date"] = $paytime;
        if ($paytime !== "") $arr["close_date"] = $closetime;
        $this->update(array("id" => $id), $arr);

    }

    //根据指定关闭时间取得订单

    public function ChangeState_id($id, $state, $paytime = "", $closetime = "")
    {
        $arr["state"] = $state;
        $arr["pay_date"] = $state;
        if ($paytime !== "") $arr["pay_date"] = $paytime;
        if ($paytime !== "") $arr["close_date"] = $closetime;
        $this->update(array("order_id" => $id), $arr);

    }

    //更改订单的状态

    public function GetOrderByParm($really_price, $state, $type, $parm = "*")
    {

        return $this->find(array("really_price" => $really_price, "state" => $state, "type" => $type), "", $parm);

    }

    public function Insert($data)
    {
        $this->create($data);//创建一条记录
    }

    //关闭过期订单

    public function CreateOrder($arg)
    {


        $this->closeEndOrder();
        //校验关卡,校验订单，取得支付二维码

        if (!$this->OrderChcek($arg))
            return false;
        if (!$this->GetPayMoney($arg["price"], $arg["type"]))
            return false;
        if (!$this->GetPayPic())
            return false;
        $createDate = time();

        $app = new App();

        $appdata = $app->getData($this->appid, "notify_url,return_url");

        $data = array(//入库数据准备完毕
            "close_date" => 0,
            "create_date" => $createDate,
            "is_auto" => $this->isAuto,
            "notify_url" => $appdata["notify_url"],
            "order_id" => $this->orderId,
            "param" => $this->param,
            "pay_date" => 0,
            "pay_id" => $this->payId,
            "pay_url" => $this->payUrl,
            "price" => $this->price,
            "really_price" => $this->reallyPrice,
            "return_url" => $appdata["return_url"],
            "state" => 0,
            "type" => $this->type,
            "appid" => $this->appid
        );

        $this->insert_ignore($data);//直接插入数据库

        if ($this->isHtml == 1) {//使用自带的支付接口
            return array("code" => 1, "msg" => 'success', "url" => url("api/pay", "index") . "?orderId=" . $this->orderId, "isHtml" => true);
        } else {//不使用呗
            $conf = new Config();
            $time = $conf->GetData(Config::close);
            $data = array(
                "payId" => $this->payId,
                "orderId" => $this->orderId,
                "payType" => $this->type,
                "price" => $this->price,
                "reallyPrice" => $this->reallyPrice,
                "payUrl" => $this->payUrl,
                "isAuto" => $this->isAuto,
                "state" => 0,
                "timeOut" => $time,
                "date" => $createDate
            );
            return array("code" => 0, "msg" => 'success', "data" => $data, "isHtml" => false);
        }


    }

    //根据价格，状态，类型取得订单信息，这个是app推送该订单信息，进行查询的

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


        $this->Clear_Order($close_time, $close_date);

        $rows = $this->GetOrderClose($close_date, "order_id");//取得今天关闭的订单id

        if ($rows) {//如果有

            $tmp = new temp();

            foreach ($rows as $row) {
                $tmp->temp_del($row['order_id']);
            }

            //删除临时表中的价格信息

            $rows = $tmp->temp_getAll();
            if ($rows) {
                foreach ($rows as $row) {
                    $re = $this->GetOrder($row["oid"], "id");//没有该记录
                    if (!$re) {
                        $tmp->temp_del($row["oid"]);
                    }
                }
            }
        }

        // echo json_encode(array("code"=>1,"msg"=>"清理完成！"));

    }

    //插入一条订单信息

    public function Clear_Order($time, $close_date)
    {//关闭过期订单

        $this->update(array("create_date <= :create_date and state = 0", "create_date" => $time), array("state" => -1, "close_date" => $close_date));

    }


    //创建订单

    public function GetOrderClose($data, $parm = "*")
    {

        return $this->findAll(array("close_date" => $data), "", $parm);

    }

    //取得订单的错误信息

    public function GetOrder($id, $parm = "*")
    {

        return $this->find(array("id" => $id), "", $parm);

    }

    //对创建订单的参数进行检查

    private function OrderChcek($arg)
    {
        $this->appid = $arg["appid"];
        if (!$this->appid || $this->appid === "") {
            $this->err = "请传入appid";
            return false;
        }
        $this->payId = $arg["payId"];
        if (!$this->payId || $this->payId === "") {
            $this->err = "请传入商户订单号";
            return false;

        }
        $this->type = $arg["type"];
        if (!$this->type || $this->type === "") {
            $this->err = "请传入支付方式=>1|微信 2|支付宝";
            return false;

        }
        if ($this->type != 1 && $this->type != 2) {
            $this->err = "支付方式错误=>1|微信 2|支付宝";
            return false;
        }

        $this->price = $arg["price"];
        if (!$this->price || $this->price === "") {
            $this->err = "请传入订单金额";
            return false;
        }
        if ($this->price <= 0) {
            $this->err = "订单金额必须大于0";
            return false;

        }

        $this->sign = $arg["sign"];
        if (!$this->sign || $this->sign === "") {
            $this->err = "请传入签名";
            return false;
        }

        $this->isHtml = $arg["isHtml"];
        if (!$this->isHtml || $this->isHtml === "") {
            $this->isHtml = 0;
        }
        $this->param = $arg["param"];
        if (!$this->param) {
            $this->param = "";
        }
        //最后校验签名
        if ($this->CheckSign()) return true;
        else return false;
    }

    //对订单的sign进行检查,前提是这些参数必须进行上一步检查通过
    private function CheckSign()
    {
        $alipay = new AlipaySign();

        $arr["payId"] = $this->payId;
        $arr["price"] = $this->price;
        $arr["param"] = $this->param;
        $arr["type"] = $this->type;
        $arr["appid"] = $this->appid;
        $arr["isHtml"] = $this->isHtml;
        //TODO 修改签名部分
        $app = new App();

        $res = $app->getData($this->appid, "connect_key");

        if (!$res) {
            $this->err = "该应用id不存在，请到后台创建";
            return false;
        }

        $this->connect_key = $res["connect_key"];

        $arr["key"] = $this->connect_key;

        $_sign = $alipay->getSign($arr, $this->connect_key);

        if (md5($_sign) !== md5($this->sign)) {
            $this->err = "sign校验失败";
            return false;
        }
        //此时应该校验客户端是否在线上
        $conf = new Config();
        $jkstate = $conf->GetData(Config::jkstate);
        if ($jkstate !== "1") {
            $this->err = "监控状态异常";
            return false;
        }
        if ($this->GetOrder($this->payId, "pay_id")) {
            $this->err = "请不要重复提交该订单,该订单已经存在";
            return false;
        }
        return true;
    }

    //获取支付二维码，成功返回二维码，不成功返回false

    private function GetPayMoney($price, $type)
    {


        $reallyPrice = bcmul($price, 100);

        $conf = new Config();

        $payQf = $conf->GetData(Config::payQf);//如何区分订单？

        $this->orderId = date("YmdHms") . rand(1, 9) . rand(1, 9) . rand(1, 9) . rand(1, 9);

        $ok = false;//找到一个不重复的金额

        for ($i = 0; $i < 10; $i++) {
            $tmpPrice = $reallyPrice . "-" . $type;

            $temp = new temp();

            $res = $temp->temp_insert(array("price" => $tmpPrice, "oid" => $this->orderId));


            if ($res) {
                $ok = true;
                break;
            }

            if ($payQf === 1) {//采用递增来区分价格
                $reallyPrice++;
            } else {//反之递减
                $reallyPrice--;
            }
        }

        //如果循环10次之后还是找不到对应的价格的话，那么返回错误

        if (!$ok) {
            $this->err = "该时间段订单量过大，请稍后重试";
            return false;
        } else {
            $reallyPrice = bcdiv($reallyPrice, 100, 2);
            $this->reallyPrice = $reallyPrice;
            return true;//这是真实的价格
        }

    }

    //获取到实际支付的价格，防止订单混乱，在同一时间段出现支付情况时,在随机在0.00-0.10之间徘徊

    private function GetPayPic()
    {//取得支付的图片

        $conf = new Config();

        $payUrl = "";

        $this->isAuto = 0;

        if ($this->type === "2") {//看看是不是支付宝
            $Uid = $conf->GetData(Config::uid);//看看有没有uid
            if ($Uid !== "") {//有uid直接任意二维码


                $str = "alipays://platformapi/startapp?appId=20000123&actionType=scan&biz_data={\"s\": \"money\",\"u\": \"[PID]\",\"a\": \"[MONEY]\",\"m\":\"[BEI]\"}";
                $user = $Uid;
                $str = str_replace("[PID]", $user, $str);
                $str = str_replace("[MONEY]", $this->reallyPrice, $str);
                $payUrl = urlencode(str_replace("[BEI]", $this->param, $str));


                $this->isAuto = 3;//支付宝自动的

                //$this->err=$payUrl;
            }
        }

        //第一波取二维码结束，看看有没有成功
        if ($payUrl === "") {

            $pay = new PayCode();

            $_payUrl = $pay->GetCodeOnly($this->price, $this->type);//根据金额取得二维码

            if ($_payUrl) {
                $payUrl = $_payUrl['pay_url'];//存在该金额的二维码
                $this->isAuto = 1;//数据库存在的自动化

                //$this->err=$payUrl;
            }
        }
        //第二波取二维码结束，看看有没有成功
        if ($payUrl === "") {
            if ($this->type === "1") {
                $payUrl = $conf->GetData(Config::wxpay);

            } else {
                $payUrl = $conf->GetData(Config::zfbpay);
            }
            //$this->err=$payUrl;
        }
        //第三波取二维码结束，看看有没有成功
        if ($payUrl === "") {//我都这么努力了还没有二维码，哭了
            $this->err = "请管理员进入后台配置收款信息后再试";
            return false;
        } else {
            $this->payUrl = $payUrl;//取到二维码啦
            return true;
        }
    }

    public function GetErr()
    {
        return $this->err;
    }
}
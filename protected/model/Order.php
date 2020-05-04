<?php
namespace app\model;
use app\includes\AlipaySign;
use app\includes\Email;
use app\includes\Web;
use app\lib\speed\mvc\Model;

/*订单处理模块
 * */

class Order extends Model
{
    //订单状态常量定义
    const State_Succ = 3;//远程服务器回调成功，订单完成确认
    const State_Err = 2;//通知失败,回调服务器没有返回正确的响应信息
    const State_Ok = 1;//支付完成，通知成功
    const State_Wait = 0;//订单等待支付中
    const State_Over = -1;//订单超时

    const NeedHtml=1;//需要html
    const NeedData=0;//我只要支付相关的数据
    //支付方式
    const PayWechat=1;
    const PayAlipay=2;
    //订单构成元素
    private $payId;//远程服务器的订单号
    private $type;//支付方式
    private $price;//实际支付的价格
    private $sign;//签名
    private $isHtml;//是否跳转本地支付接口（1）（不需要额外写界面），（为0返回创建的数据包）。
    private $param;//附加参数
    private $payUrl;//支付二维码的URL
    private $appid;//订单由哪个应用创建，标记创建应用，有时候多项目用得到
    private $key;//与该应用关联的通讯密钥
    private $isAuto=false;//是不是需要手动输入金额
    private $orderId;//本程序创建的订单号
    private $reallyPrice;//真实收到的钱
    private $explain=null;//参数中的说明信息

    //模块变量
    private $err;//订单产生的错误均放在这里

    //指定表
    public function __construct()
    {
        parent::__construct("pay_order");
    }

    //后台响应，获取所有的订单
    public function getOrders($page, $limit, $type = "", $state = "")
    {
        $conditon = NULL;
        if ($type !== "") $conditon["type"] = $type;
        if ($state !== "") $conditon["state"] = $state;

        return $this->selectAll($conditon, "id desc", "*", array($page, $limit));
    }

    //根据id删除订单，并同时删除tmp表格中的信息
    public function delOrderById($id)
    {
        $res = $this->select(array("id" => $id),null,'order_id,state');
        if ($res) {
            $this->delete(array("id" => $id));
            $this->reset("pay_tmp_price");
            $this->delete(array("oid" => $res['order_id']));
            $this->reset("pay_order");

        }

    }
    //根据payid删除订单，并同时删除tmp表格中的信息
    public function delOrderByPayId($id)
    {
        $res = $this->select(array("pay_id" => $id),null,'order_id,state');
        if ($res) {
            $this->delete(array("pay_id" => $id));
            $this->reset("pay_tmp_price");
            $this->delete(array("oid" => $res['order_id']));
            $this->reset("pay_order");

        }

    }
    //删除标记过期的订单
    public function delOverOrder()
    {
        $this->delete(array("state" => self::State_Over));

    }

    //删除检查超过7天的订单
    public function delLastOrder()
    {

        $this->delete(array("create_date <:create_date", ":create_date" => time() - 604800));

    }
    //根据id取得指定订单信息
    public function getOrderById($id, $param = "*")
    {

        return $this->select(array("id" => $id), "", $param);

    }
    //根据orderid取得指定订单信息
    public function getOrderByOrdid($id, $param = "*")
    {

        return $this->select(array("order_id" => $id), "", $param);

    }
    //根据payid取得指定订单信息
    public function getOrderByPayId($id, $param = "*")
    {

        return $this->select(array("pay_id" => $id), "", $param);

    }

    //根据id修改订单状态

    public function changeStateById($id, $state, $paytime = "", $closetime = "")
    {
        $arr["state"] = $state;
        $arr["pay_date"] = $state;
        if ($paytime !== "") $arr["pay_date"] = $paytime;
        if ($closetime !== "") $arr["close_date"] = $closetime;
        $this->update(array("id" => $id), $arr);

    }
//根据payid修改订单状态
    public function changeStateByPayId($id, $state, $paytime = "", $closetime = "")
    {
        $arr["state"] = $state;
        $arr["pay_date"] = $state;
        if ($paytime !== "") $arr["pay_date"] = $paytime;
        if ($closetime !== "") $arr["close_date"] = $closetime;
        $this->update(array("pay_id" => $id), $arr);

    }

    //根据orderid修改订单状态

    public function changeStateByOrderId($id, $state, $paytime = "", $closetime = "")
    {
        $arr["state"] = $state;
        $arr["pay_date"] = $state;
        if ($paytime !== "") $arr["pay_date"] = $paytime;
        if ($closetime !== "") $arr["close_date"] = $closetime;
        $this->update(array("order_id" => $id), $arr);

    }

    //根据价格，状态，类型取得订单信息，这个是app推送该订单信息，进行查询的

    public function getOrderByParam($really_price, $state, $type, $parm = "*")
    {

        return $this->select(array("really_price" => $really_price, "state" => $state, "type" => $type), "", $parm);

    }


    //创建订单

    public function createOrder($arg)
    {
        $this->closeEndOrder();
        if($this->isOffline()){
            $this->err="系统错误，暂时不能支付！";//监控掉线
            return false;
        }
        //校验关卡,校验订单
        if (!$this->orderCheck($arg))return false;
        //关闭重复订单,避免多次提交导致支付失败
        $this->delOrderByPayId($this->payId);
        //订单生成时间
        $createDate = time();
        $conf = new Config();
        $time = $conf->getData(Config::ValidityTime);//订单超时时间
        //计算超时时间，上述time单位为分钟
        $timeout=intval($time)*60+$createDate;
        //获得真实的支付金额
        if (!$this->getPayMoney($arg["price"], $arg["type"],$timeout))return false;
        //对参数进行解码，进行url编码防止传输过程中中断
        $json=json_decode(urldecode($arg["param"]));

        //取得支付二维码
        if (!$this->getPayPic())return false;

        //入库数据数组
        $data = array(//入库数据准备完毕
            "close_date" => 0,//订单关闭时间
            "create_date" => $createDate,
            "order_id" => $this->orderId,
            "param" => $this->param,
            "pay_date" => 0,//订单支付时间
            "pay_id" => $this->payId,
            "price" => $this->price,
            "really_price" => $this->reallyPrice,
            "state" => self::State_Wait,//等待支付状态
            "type" => $this->type,
            "appid" => $this->appid,
            "payUrl" => $this->payUrl,
            "isAuto"=>$this->isAuto
        );

        $this->insertIgnore($data);
        //直接插入数据库

        if ($this->isHtml == self::NeedHtml) {//使用自带的支付接口
            return array("code" => Config::Api_Ok, "msg" => 'success', "url" => url("api/pay", "index",array("orderId"=>$this->orderId)) , "isHtml" => true);
        } else {//不使用呗
               $data = array(
                "payId" => $this->payId,
                "orderId" => $this->orderId,
                "payType" => $this->type,
                "price" => $this->price,
                "reallyPrice" => $this->reallyPrice,
                "payUrl" => $this->payUrl,
                "state" => self::State_Wait,
                "timeOut" => $timeout,
                "date" => $createDate,
                "isAuto"=>$this->isAuto

               );
            return array("code" => Config::Api_Ok, "msg" => 'success', "data" => $data, "isHtml" => false);
        }


    }

    //关闭过期订单
    public function closeEndOrder()
    {

        $conf = new Config();

        $close_time = $conf->getData(Config::ValidityTime);//订单关闭的时间

        $close_time = time() - 60 * $close_time;//计算订单关闭时间

        $close_date = time();

        $this->clearOrder($close_time, $close_date);

        //清理临时表中的过期信息
        $temp=new Temp();
        $temp->DelTimeOut();


    }

    //根据时间清理

    public function clearOrder($time, $close_date)
    {

        $this->update(array("create_date <= :create_date and state = ".self::State_Wait, "create_date" => $time), array("state" => self::State_Over, "close_date" => $close_date));

    }


    //取得已经关闭的订单

    public function getOrderClose($data, $parm = "*")
    {

        return $this->selectAll(array("close_date" => $data), "", $parm);

    }


    //通知远程服务器，我已经收到钱了
    public function notify($id){

        $res = $this->getOrderByOrdid($id, "appid,pay_id,order_id,type,param,price,really_price,state");

        if ($res) {

            //在通知远程服务器之前，我们收到了app的推送，故认为已经收到钱了，所以先更新支付状态，再通知服务器
            $this->ChangeStateByOrderId($id, Order::State_Ok);

            //更新状态为已经支付
            $tmp = new Temp();

            $tmp->DelByOid($id);

            //不管是不是已支付（我觉得你付过了~不然调用我干嘛），直接删除临时表里面的内容
            $app = new App();

            $AppRes = $app->getData($res["appid"], "connect_key,notify_url");

            $key = $AppRes['connect_key'];
            //取通讯密钥
            $url = $this->replace($AppRes['notify_url'],$res['pay_id'],$res['order_id']);
            //取回调地址
            $arr["payId"] = $res['pay_id'];
            $arr["param"] = $res['param'];
            $arr["type"] = $res['type'];
            $arr["price"] = $res['price'];
            $arr["reallyPrice"] = $res['really_price'];
            //key只做加盐 
           // $arr["key"] = $key;
            //参数化
            $alipay = new AlipaySign();

            $sign = $alipay->getSign($arr, $key);

            $arr["sign"] = $sign;
            //key只是作为加盐参数，不作为传递参数，所以此处剔除
           // $arr = array_diff_key($arr, array("key" => $key));
            
            $web = new web();


            //悄悄告诉远程服务器，我收到钱了，为了防止别人仿冒我，要加上密钥进行验证

            $re1 = $web->get($url,$arr);

            $re = json_decode($re1);

            if ($re) {
                //远程服务器响应正常，表示认可
                if ($re->state===Config::Api_Ok) {
                    //告诉响应接口，好啦响应是成功的~
                    //发邮件啦啦啦
                    $conf=new Config();
                    $mailAddr=$conf->getData('MailRec');
                    if($conf->getData('MailNoticeYou')==='on'&&Email::isEmail($mailAddr)){
                        $mail=new Email();
                        ob_end_clean();
                        $json=json_decode(urldecode($arr["param"]));
                        if($json){
                            dump($json);
                        }else dump(urldecode($arr["param"]));

                        $content=<<<EOF
支付金额： ￥{$arr["price"]}<br>
实际支付： <font color="red">￥{$arr["reallyPrice"]}</font><br>
其他参数：<br>
EOF;
                        $content=$content.ob_get_contents();
                        ob_end_clean();
                       
                        $mail->send($mailAddr,'用户支付通知',$content,'Vpay');
                    }
                    return json_encode(array("code" => Config::Api_Ok, "msg" => $re->msg));
                } else {
                    //远程服务器不认可？？？凭啥？我也不知道呀~

                    $this->ChangeStateByOrderId(arg("id"), Order::State_Err);
                    return json_encode(array("code" => Config::Api_Err, "msg" => $re->msg."苍天饶过谁"));
                }

            } else {
                $conf=new Config();
                $mailAddr=$conf->getData('MailRec');
                if(Email::isEmail($mailAddr)){
                    $mail=new Email();
                    $content=<<<EOF
通知地址： {$url}<br>
返回结果：<br>
{$re1}
EOF;
                    $mail->send($mailAddr,'支付通知失败',$content,'Vpay');
                }
                $this->ChangeStateByOrderId(arg("id"), Order::State_Err);
                return json_encode(array("code" => Config::Api_Err, "msg" => "异步回调返回的数据不是标准json数据！"));
            }
        } else {
            //啥？你要我通知服务器这个不存在的订单？
            return json_encode(array("code" => Config::Api_Err, "msg" => "订单不存在"));
        }

    }
    //对创建订单的参数进行检查

    private function orderCheck($arg)
    {

        if (!isset($arg["appid"])) {
            $this->err = "请传入appid";
            return false;
        }
        $this->appid = strval($arg["appid"]);

        if (!isset($arg["payId"])) {
            $this->err = "请传入商户订单号";
            return false;
        }
        $this->payId = strval($arg["payId"]);

        if (!isset($arg["type"])) {
            $this->err = "请传入支付方式=>1|微信 2|支付宝";
            return false;
        }
        $this->type = intval($arg["type"]);
        if ($this->type != 1 && $this->type != 2) {
            $this->err = "支付方式错误=>1|微信 2|支付宝";
            return false;
        }


        if (!isset($arg["price"])) {
            $this->err = "请传入订单金额";
            return false;
        }
        $this->price = floatval($arg["price"]);
        if ($this->price <= 0) {
            $this->err = "订单金额必须大于0";
            return false;
        }
        if (!isset($arg["sign"])) {
            $this->err = "请传入签名";
            return false;
        }
        $this->sign = strval($arg["sign"]);

        if (!isset($arg["isHtml"])) {
            $this->isHtml = self::NeedData;
        }
        $this->isHtml = intval($arg["isHtml"]);

        if (isset($arg["param"])) {
            $this->param = strval($arg["param"]);
        }else $this->param = "";
        //参数中设置了收款原因，只在支付宝自动金额收款有效，只能20个字符左右
        if(isset($arg["explain"]))$this->explain=substr(strval($arg["explain"]),0,20);
        //最后校验签名
        if ($this->CheckSign()) return true;
        else return false;
    }

    //对订单的sign进行检查,前提是这些参数必须进行上一步检查通过
    private function checkSign()
    {
        //查找这个app
        $app = new App();
        $res = $app->getData($this->appid, "connect_key");
        if (!$res) {
            $this->err = "该应用id不存在，请到后台创建";
            return false;
        }

        $this->key = $res["connect_key"];
        //封装用来签名的数组
        $arr["payId"] = $this->payId;
        $arr["price"] = $this->price;
        $arr["param"] = $this->param;
        $arr["type"] = $this->type;
        $arr["appid"] = $this->appid;
        $arr["isHtml"] = $this->isHtml;
        if($this->explain!==null)
            $arr["explain"] = $this->explain;

        //为了安全key只做加盐
        //$arr["key"] = $this->key;
        //准备签名
        $alipay = new AlipaySign();
        $_sign = $alipay->getSign($arr, $this->key);

        if (md5($_sign) !== md5($this->sign)) {
            $this->err = "签名校验失败";
            return false;
        }
        return true;
    }

    //获取到实际支付的价格，防止订单混乱，在同一时间段出现支付情况时,在随机在0.00-0.10之间徘徊

    private function getPayMoney($price, $type,$timeout)
    {
        $price=floatval($price);

        $reallyPrice = intval(bcmul($price, 100));


        $conf = new Config();

        $payQf = $conf->getData(Config::Payof);//如何区分订单？

        $this->orderId = date("YmdHms") . rand(1, 9) . rand(1, 9) . rand(1, 9) . rand(1, 9);

        //生成云端订单号



        //查找临时表中的不重复金额
        for ($i = 0; $i < 10; $i++) {
            $tmpPrice = $reallyPrice . "-" . $type;

            $temp = new Temp();

            $res = $temp->add(array("price" => $tmpPrice, "oid" => $this->orderId,"timeout"=>$timeout));//返回尝试插入结果，false表示已经存在


            //true表示该时间段没有
            if ($res) break;

            if (intval($payQf) === Config::PayIncrease) {//采用递增来区分价格
                $reallyPrice++;
            } else {//反之递减
                $reallyPrice--;
            }
            if($reallyPrice<=0){
                $this->err = "该时间段订单量过大，请换个时间尝试重试";
                return false;
            }
        }

        //如果循环10次之后还是找不到对应的价格的话，那么返回错误

        if ($i>=10) {
            $this->err = "该时间段订单量过大，请换个时间尝试重试";
            return false;
        } else {
            $reallyPrice = bcdiv($reallyPrice, 100, 2);
            $this->reallyPrice = $reallyPrice;
            return true;//这是真实的价格
        }

    }

    private function getPayPic()
    {//取得支付的图片

        $conf = new Config();

        $payUrl = "";

        //不是移动端访问时才使用这个
        if ($this->type === self::PayAlipay) {//看看是不是支付宝
            $user = $conf->getData(Config::Ailuid);//看看有没有uid
            if ($user !== "") {//有uid直接任意二维码
                $str = "alipays://platformapi/startapp?appId=09999988&actionType=toAccount&goBack=NO&amount=[MONEY]&userId=[PID]&memo=[EXP]";
                //fix bug
                $str = str_replace("[PID]", $user, $str);
                $str = str_replace("[MONEY]", $this->reallyPrice, $str);
                $payUrl = urlencode(str_replace("[EXP]", $this->explain, $str));
                $this->isAuto=true;

            }
        }
        //第一波取二维码(支付宝自动)结束，看看有没有成功
        if ($payUrl === "") {

            $pay = new PayCode();

            $_payUrl = $pay->getCodeOnly($this->reallyPrice, $this->type);//根据金额取得二维码，fix bug

            if ($_payUrl)$payUrl = $_payUrl['pay_url'];//存在该金额的二维码
            $this->isAuto=false;
        }
        //第二波取二维码（上传的支付宝微信收款码）结束，看看有没有成功
        if ($payUrl === "") {
            if ($this->type === self::PayWechat)
                $payUrl = $conf->getData(Config::WechatPay);
            else $payUrl = $conf->getData(Config::AliPay);
            $this->isAuto=true;
        }
        //第三波取二维码（预存的任意金额收款码）结束，看看有没有成功
        if ($payUrl === "") {//我都这么努力了还没有二维码，哭了
            $this->err = "请管理员进入后台配置收款信息后再试.";
            return false;
        } else {
            $this->payUrl = $payUrl;//取到二维码啦
            return true;
        }
    }

    public function getErr()
    {
        return $this->err;
    }

    /**
     * 检查监控端是否掉线
     */
    public function isOffline(){
        $conf=new Config();
        $t=$conf->getData(Config::LastHeart);
        $jg = time()  - intval($t);

        if ($jg > 120 || $jg < -120) {
            $conf->setData("State", Config::State_Offline);//表示掉线
            //准备通知
            $mailAddr=$conf->getData('MailRec');
            if($conf->getData('MailNoticeMe')==='on'&&Email::isEmail($mailAddr)){
                $mail=new Email();
                $content=<<<EOF
监控端已经掉线<br>
请及时上线！<br>
Tips:默认120秒内客户端没有发送心跳请求则认为客户端掉线<br>
EOF;
                $mail->send($mailAddr,'手机端监控掉线通知',$content,'Vpay');
            }
            log($jg);
            return true;//掉线返回true
        }
        return false;
    }
    /*
     * 替换url中的自定义变量
     * */
    public function replace($url,$payId,$orderId){
       $url=str_replace('{payId}',$payId,$url);
        $url=str_replace('{orderId}',$orderId,$url);
        return $url;
    }
}
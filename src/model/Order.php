<?php
namespace app\model;
use app\attach\AlipaySign;
use app\attach\ConstData;
use app\attach\Email;
use app\core\config\Config;
use app\core\debug\Log;
use app\core\mvc\Model;
use app\core\utils\StringUtil;
use app\core\web\Response;
use app\lib\Alipay\AlipayServiceSend;
use app\lib\HttpClient\HttpClient;

/*订单处理模块
 * */

class Order extends Model
{

    //订单构成元素
    private $payId;//远程服务器的订单号
    private $price;//实际支付的价格
    private $sign;//签名
    private $isHtml;//是否跳转本地支付接口（1）（不需要额外写界面），（为0返回创建的数据包）。
    private $param;//附加参数
  //  private $payUrl;//支付二维码的URL
    private $appid;//订单由哪个应用创建，标记创建应用，有时候多项目用得到
    private $key;//与该应用关联的通讯密钥
    private $isAuto=false;//是不是需要手动输入金额
    private $orderId;//本程序创建的订单号
    private $explain=null;//参数中的说明信息
     private  $reallyPrice;//真实需要支付的金额
    private $notifyUrl;//异步通知
    private $returnUrl;//同步通知
    //模块变量
    private $err;//订单产生的错误均放在这里

    //指定表
    private $t;


    public function __construct()
    {
        parent::__construct("pay_order");
    }

    /**
     * 后台响应，获取所有的订单
     * @param $page int 第几页
     * @param $limit int 每页数量
     * @param string $state 订单状态
     * @param string $app appId
     * @return array|int
     */
    public function getOrders(int $page, int $limit,  string $state = "", string $app = "")
    {
        $condition = [];
        if ($state !== "") $condition["state"] = $state;
        if ($app !== "") $condition["state"] = $state;
        return $this->select()->where($condition)->page($page, $limit)->orderBy("id desc")->commit();
    }

    /**
     * 根据id删除订单，并同时删除tmp表格中的信息
     * @param $id
     */
    public function delOrderById($id)
    {
        $res = $this->select('order_id,state')->where(["id" => $id])->commit();
        if(!empty($res)){
            $this->delete()->where(["id" => $id])->commit();
        }
    }

    /**
     * 根据payid删除订单，并同时删除tmp表格中的信息
     * @param $id
     */
    public function delOrderByPayId($id)
    {
        $res = $this->select('order_id,state')->where(["pay_id" => $id])->commit();
        if(!empty($res)){
            $this->delete()->where(["pay_id" => $id])->commit();
         //   $this->delete()->table("pay_tmp_price")->where(["oid" => $res[0]['order_id']])->commit();
        }

    }

    /**
     * 删除标记过期的订单
     */
    public function delOverOrder()
    {
        $this->delete()->where(["state" => ConstData::StateOver])->commit();

    }

    /**
     * 根据id取得指定订单信息
     * @param $id
     * @return array|int
     */
    public function getOrderById($id)
    {

        return $this->select()->where(["id" => $id])->commit();

    }

    /**
     * 根据orderid取得指定订单信息
     * @param $id
     * @return array|int
     */
    public function getOrderByOrdid($id)
    {
        return $this->select()->where(["order_id" => $id])->commit();
    }

    /**
     * 根据payid取得指定订单信息
     * @param $id
     * @return array|int
     */
    public function getOrderByPayId($id)
    {
        return $this->select()->where(["pay_id" => $id])->commit();

    }

    /**
     * 根据id修改订单状态
     * @param $id
     * @param $state
     * @param string $paytime
     * @param string $closetime
     */

    public function changeStateById($id, $state, $paytime = "", $closetime = "")
    {
        $arr["state"] = $state;
        $arr["pay_date"] = $state;
        if ($paytime !== "") $arr["pay_date"] = $paytime;
        if ($closetime !== "") $arr["close_date"] = $closetime;
        $this->update()->where(["id" => $id])->set($arr)->commit();

    }

    /**
     * 根据payid修改订单状态
     * @param $id
     * @param $state
     * @param string $paytime
     * @param string $closetime
     */
    public function changeStateByPayId($id, $state, $paytime = "", $closetime = "")
    {
        $arr["state"] = $state;
        $arr["pay_date"] = $state;
        if ($paytime !== "") $arr["pay_date"] = $paytime;
        if ($closetime !== "") $arr["close_date"] = $closetime;
        $this->update()->where(["pay_id" => $id])->set($arr)->commit();
    }

    /**
     * 根据orderid修改订单状态
     * @param $id
     * @param $state
     * @param string $paytime
     * @param string $closetime
     */

    public function changeStateByOrderId($id, $state, $paytime = "", $closetime = "")
    {
        $arr["state"] = $state;
        $arr["pay_date"] = $state;
        if ($paytime !== "") $arr["pay_date"] = $paytime;
        if ($closetime !== "") $arr["close_date"] = $closetime;
        $this->update()->where(["order_id" => $id])->set($arr)->commit();
    }



    /**
     * 创建订单
     * @param $arg array
     * @return array|false
     */

    public function createOrder(array $arg)
    {
        $this->closeEndOrder();
        //校验关卡,校验订单
        if (!$this->orderCheck($arg))return false;
        //关闭重复订单,避免多次提交导致支付失败
        $this->delOrderByPayId($this->payId);
        //订单生成时间
        $createDate = time();
        //订单超时时间
        $time = Config::getInstance("pay")->getOne("pay")["validity_minute"];
        //计算超时时间，上述time单位为分钟
        $timeout=intval($time)*60+$createDate;
        //获得真实的支付金额，进行优惠券核销
        $this->getPayMoney($this->price);
        //生成内部订单
        $this->orderId = date("YmdHms") . rand(1, 9) . rand(1, 9) . rand(1, 9) . rand(1, 9);
        //生成支付地址
      //  $this->payUrl = Response::getAddress();
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
            "state" => ConstData::StateWait,//等待支付状态
            "title" => $this->explain,
            "appid" => $this->appid,
         //   "payUrl" => $this->payUrl,
            "isAuto"=>$this->isAuto,
            "notifyUrl" => $this->notifyUrl,
            "returnUrl"=>$this->returnUrl
        );



        $this->insert(SQL_INSERT_NORMAL)->keyValue($data)->commit();
        //直接插入数据库
        if($this->reallyPrice==0){
            $this->notify( $this->orderId);
            //如果已经ok，直接通知成功
            return [
                "code" => ConstData::ApiOk,
                "msg" => 'success',
                "data" =>[
                    "state" => ConstData::StateSuccess,
                    "url" => Response::getAddress()."/ui/pay/#?orderId=".$this->orderId,
                    "isHtml" => $this->isHtml == ConstData::NeedHtml
                ]
            ];
        }
        $bool = $this->createAlipay();
        if(!$bool){
            $this->delOrderByPayId($this->payId);
            return [
                "code" => ConstData::ApiError,
                "msg" => '本系统今日已达交易上限，请于明日再试。',
                "data" =>[]
            ];
        }
        if ($this->isHtml == ConstData::NeedHtml) {//使用自带的支付接口
            return [
                "code" => ConstData::ApiOk,
                "msg" => 'success',
                "data" =>[
                    "state" => ConstData::StateWait,
                    "url" => Response::getAddress()."/ui/pay/#?orderId=".$this->orderId,
                    "isHtml" => true
                ]
            ];

        } else {//不使用呗
               $data = array(
                "payId" => $this->payId,
                "orderId" => $this->orderId,
                "title" => $this->explain,
                 "param" => $this->param,
                 "price" => $this->price,
                "reallyPrice" => $this->reallyPrice,
               // "payUrl" => $this->payUrl,
                "state" => ConstData::StateWait,
                "timeOut" => $timeout,
                "date" => $createDate,
                "isAuto"=>$this->isAuto

               );
            return [
                "code" => ConstData::ApiOk,
                "msg" => 'success',
                "data" =>[
                    "state" => ConstData::StateWait,
                    "data" => $data,
                    "isHtml" => false
                ]
            ];
        }
    }

    public function createAlipay(): bool
    {
        $pay = Config::getInstance("pay")->get();
        $appid = $pay["pay"]["alipay_id"];
        $saPrivateKey = $pay["pay"]["alipay_private_key"];
        $timeout = $pay["pay"]["validity_minute"];
        $aliPay = new AlipayServiceSend($appid, $saPrivateKey);
        $result = $aliPay->doPay($this->reallyPrice, $this->orderId, $this->explain, url("api","alipay","notify"),$timeout);
       // dump($result,true);
        $result = $result['alipay_trade_precreate_response'];
        if ($result['code'] && $result['code'] == '10000') {
            //生成二维码
            $this->update()->set(["img"=>$result['qr_code']])->where(["order_id"=>$this->orderId])->commit();
            return true;
        } else {
            return false;
        }
    }

    //关闭过期订单
    public function closeEndOrder()
    {

        $pay = Config::getInstance("pay")->get();

        $close_time = $pay["pay"]["validity_minute"];//订单关闭的时间

        $close_time = time() - 60 * $close_time;//计算订单关闭时间

        $close_date = time();

        $this->clearOrder($close_time, $close_date);


    }

    //根据时间清理

    public function clearOrder($time, $close_date)
    {
        $this->update()->where(
            ["create_date <= :create_date and state = :state",
                ":state"=>ConstData::StateWait,
                ":create_date" => $time
            ])->set(
                ["state" => ConstData::StateOver,
                 "close_date" => $close_date]
        )->commit();
    }


    //取得已经关闭的订单

    public function getOrderClose($close_date)
    {

        return $this->select()->where(["close_date" => $close_date])->commit();

    }


    //通知远程服务器，我已经收到钱了
    public function notify($id): array
    {
        //支付宝回调通知
        $res = $this->getOrderByOrdid($id);

        if (!empty($res)) {
            $res = $res[0];
           $state = $res["state"];
           if(intval($state)==ConstData::StateSuccess){
               return ["code"=>ConstData::ApiOk,"msg"=>"该订单已经回调成功，请勿重复回调"];
           }
            //在通知远程服务器之前，我们收到了app的推送，故认为已经收到钱了，所以先更新支付状态，再通知服务器
            $this->ChangeStateByOrderId($id, ConstData::StateOk);
            //不管是不是已支付（我觉得你付过了~不然调用我干嘛），直接删除临时表里面的内容
            $app = new App();

            $AppRes = $app->getData($res["appid"], "connect_key");

            if(empty($AppRes)){
                return ["code"=>ConstData::ApiError,"msg"=>"不存在这个应用"];
            }
            $AppRes = $AppRes[0];
            $key = $AppRes['connect_key'];

            $notify_url = $res["notifyUrl"];
            //取回调地址
            $arr["payId"] = $res['pay_id'];
            $arr["param"] = urlencode(base64_encode($res['param']));
            $arr["price"] = $res['price'];
            $arr["reallyPrice"] = $res['really_price'];
            //key只做加盐 
           // $arr["key"] = $key;
            //参数化
            $alipay = new AlipaySign();

            $sign = $alipay->getSign($arr, $key);

            $arr["sign"] = $sign;

            $http = new HttpClient($notify_url);
            $http->post($notify_url,$arr);

            $res = json_decode($http->getBody());
            $pay =  Config::getInstance("pay")->get();
           // echo $http->getBody();
         //   exitApp('');
            if ($res) {
                //远程服务器响应正常，表示认可
                if ($res->state===ConstData::ApiOk) {
                    //告诉响应接口，好啦响应是成功的~
                    //发邮件啦啦啦
                    if(StringUtil::get($pay["mail"]["sendType"])->contains("1")&&Email::isEmail($pay["mail"]["receive"])){
                        $json=json_decode(urldecode($arr["param"]));
                        if($json){
                            $c=print_r($json,true);
                        }else $c=print_r((urldecode($arr["param"])),true);

                        $mail = new Email();

                        $count = doubleval($arr["price"])-doubleval($arr["reallyPrice"]);
                        $tplData = [
                            "logo" => APP_PUBLIC."ui".DS.Config::getInstance("frame")->getOne("admin").DS."img".DS."face.jpg",
                            "sitename" =>$pay["pay"]["siteName"],
                            "title" => "用户支付成功通知",
                            "body" => "<p>支付站点：{$AppRes["app_name"]}</p><p>商品信息：{$arr["title"]}</p><p>支付金额：{$arr["reallyPrice"]}</p><p>优惠金额：{$count}</p><p>其他参数：{$c}</p>"
                        ];

                        $file = $mail->complieNotify("#009688", "#fff", $tplData["logo"], $tplData["sitename"], $tplData["title"], $tplData["body"]);
                        $mail->send($pay["mail"]["receive"], "{$tplData['sitename']}", $file, $tplData['sitename']);
                    }
                    return ["code" => ConstData::ApiOk, "msg" => $res->msg];
                } else {
                    //远程服务器不认可？？？凭啥？我也不知道呀~
                    $this->ChangeStateByOrderId(arg("id"), ConstData::StateError);
                    return ["code" => ConstData::ApiError, "msg" => $res->msg."远程服务器也许没有进行正确的应答！"];
                }

            } else {
                Log::info("notify","支付回调失败！");
                Log::info("notify","通知地址： {$notify_url}！");
                Log::info("notify","返回结果： {$http->getBody()}！");

                if(StringUtil::get($pay["mail"]["sendType"])->contains("2")&&Email::isEmail($pay["mail"]["receive"])){
                    $json=json_decode(urldecode($arr["param"]));
                    if($json){
                        $c=print_r($json,true);
                    }else $c=print_r((urldecode($arr["param"])),true);

                    $mail = new Email();

                    $count = doubleval($arr["price"])-doubleval($arr["reallyPrice"]);
                    $tplData = [
                        "logo" => APP_PUBLIC."ui".DS.Config::getInstance("frame")->getOne("admin").DS."img".DS."face.jpg",
                        "sitename" =>$pay["pay"]["siteName"],
                        "title" => "支付回调失败",
                        "body" => "<p>支付站点：{$AppRes["app_name"]}</p><p>商品信息：{$arr["title"]}</p><p>支付金额：{$arr["reallyPrice"]}</p><p>优惠金额：{$count}</p><p>其他参数：{$c}</p><p>通知地址:{$notify_url}</p><p>返回结果:{$http->getBody()}</p>"
                    ];

                    $file = $mail->complieNotify("#FF5722", "#fff", $tplData["logo"], $tplData["sitename"], $tplData["title"], $tplData["body"]);
                    $mail->send($pay["mail"]["receive"], "{$tplData['sitename']}", $file, $tplData['sitename']);
                }

                $this->ChangeStateByOrderId(arg("id"), ConstData::StateError);
                return ["code" => ConstData::ApiError, "msg" => "异步回调返回的数据不是标准json数据！"];
            }
        } else {
            //啥？你要我通知服务器这个不存在的订单？
            return ["code" => ConstData::ApiError, "msg" => "订单不存在"];
        }

    }
    //对创建订单的参数进行检查

    private function orderCheck($arg): bool
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
            $this->isHtml = ConstData::NeedData;
        }
        $this->isHtml = intval($arg["isHtml"]);

        if (!isset($arg["param"])) {
            $this->err = "无其他参数部分，请传入{}，并使用URL编码";
            return false;
        }
        $this->param = urldecode(base64_decode(strval($arg["param"])));

        if(!isset($arg["explain"])){
            $this->err = "请传入收款原因";
            return false;
        }
        $this->explain = $arg["explain"];

        if(!isset($arg["notifyUrl"])){
            $this->err = "请传入异步通知地址";
            return false;
        }
        $this->notifyUrl =urldecode( $arg["notifyUrl"]);
        if(!isset($arg["returnUrl"])){
            $this->err = "请传入同步通知地址";
            return false;
        }
        $this->returnUrl = urldecode($arg["returnUrl"]);
        $this->t=$arg["t"];
        //最后校验签名
        if ($this->CheckSign()) return true;
        else return false;
    }

    //对订单的sign进行检查,前提是这些参数必须进行上一步检查通过
    private function checkSign(): bool
    {
        //查找这个app
        $app = new App();
        $res = $app->getData($this->appid, "connect_key");
        if (empty($res)) {
            $this->err = "该应用id不存在，请到后台创建";
            return false;
        }
        $res = $res[0];
        $this->key = $res["connect_key"];
        //封装用来签名的数组

        $arr["payId"] = $this->payId;
        $arr["price"] = $this->price;
        $arr["param"] = $this->param;
        $arr["appid"] = $this->appid;
        $arr["isHtml"] = $this->isHtml;
        $arr["explain"] = $this->explain;
        $arr["notifyUrl"] = $this->notifyUrl;
        $arr["returnUrl"] = $this->returnUrl;
        $arr["t"]= $this->t;
    //    dump($arr);
        //准备签名
        $alipay = new AlipaySign();
        $_sign = $alipay->getSign($arr, $this->key);

        if (md5($_sign) !== md5($this->sign)) {
            $this->err = "签名校验失败";
            return false;
        }
        return true;
    }

    //获取到实际支付的价格

    private function getPayMoney($price)
    {
        $price=doubleval($price);

        $this->reallyPrice = $price;
    }

    public function getErr()
    {
        return $this->err;
    }

    public function setUser($orderId, $user)
    {
        $this->update()->set(["userId"=>$user])->where(["order_id"=>$orderId])->commit();
    }


}
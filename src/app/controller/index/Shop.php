<?php
/*
 * Copyright (c) 2023. Ankio.  由CleanPHP4强力驱动。
 */

namespace app\controller\index;

use Ankio\objects\PayCreateObject;
use Ankio\objects\PayNotifyObject;
use Ankio\PayConfig;
use Ankio\Vpay;
use app\channels\AlipayChannel;
use app\channels\AppChannel;
use app\controller\admin\Order;

use app\database\dao\AppDao;
use app\database\dao\ShopDao;
use app\database\model\OrderModel;
use app\objects\order\CreateOrderObject;
use cleanphp\App;
use cleanphp\base\Config;
use cleanphp\base\Controller;
use cleanphp\base\Json;
use cleanphp\base\Request;
use cleanphp\base\Variables;
use cleanphp\engine\EngineManager;
use cleanphp\file\Log;
use HttpException;
use library\http\HttpClient;
use library\mail\AnkioMail;
use library\verity\VerityException;
use library\verity\VerityRule;

class Shop extends Controller
{
    public function __init()
    {
        require Variables::getLibPath("vpay","src","autoload.php");
    }

    function config(){
        $config = Config::getConfig("shop");
        $demo = [
            'notice'=>'',
            'title'=>'内置商城',
            'logo'=>'',
            'state'=>0,
            'appid'=>'',
            'appkey'=>''
        ];
        if(empty( $config)){
            $config = [];
        }
        $config = array_merge($demo,$config);
        unset($config['appkey']);
        return $this->render(200,null,$config);
    }

    function list(): string
    {
        $items = ShopDao::getInstance()->getAllItems();
        $shops = [];
        foreach ($items as $item){
            if(!isset($shops[$item['category']])){
                $shops[$item['category']] = [];
            }
            $shops[$item['category']][] = $item;
        }
        return $this->render(200,null,$shops);
    }

    function type(): string
    {
        return $this->render(200,null,[
           OrderModel::PAY_ALIPAY=> AlipayChannel::isActive(),
           OrderModel::PAY_WECHAT_APP => AppChannel::isActive(),
            OrderModel::PAY_ALIPAY_APP => AppChannel::isActive(),
        ]);
    }

    function detail(): string
    {
        $items = ShopDao::getInstance()->getById(arg("id"));
        if(!$items || $items->stop){
            return $this->render(403,"没有这个商品");
        }
        $array = $items->toArray();
        unset($array['api']);

        return $this->render(200,null,$array);
    }

    function pay(): string
    {
        $items = ShopDao::getInstance()->getById(arg("id"));
        if(!$items || $items->stop){
            return $this->render(403,"没有这个商品");
        }
        try {
            if (!VerityRule::check(VerityRule::MAIL, arg('mail'))) {
                return $this->render(403, "请输入正确的邮箱");
            }
        } catch (VerityException $e) {
            return $this->render(403, "请输入正确的邮箱");
        }
        foreach (arg() as $item) {
            if (empty($item)) {
                return $this->render(403, "请将每一项都填写完成");
            }
        }
        $id = arg("id");
        $item = ShopDao::getInstance()->getById($id);
        if (empty($item)) {
            return $this->render(404, "不存在商品");
        }
        $config = Config::getConfig("shop");

        $payConfig = new PayConfig([
            'host'=>Request::getAddress(),
            'key'=>$config['appkey'],
             'id'=>$config['appid'],
            'time'=>5
        ]);


        $pay_type = arg("type");

        $order = new PayCreateObject();
        $order->app_item = $item->item;
        $order->appid = $config['appid'];
        $order->param = Json::encode(arg());
        $order->price = $item->price;
        $order->pay_type = $pay_type;
        $order->notify_url = url("main", "shop", "notify");
        $order->return_url = Request::getAddress()."/@shop/success";
        $pay = new Vpay($payConfig);
        $result = $pay->create($order);
        if ($result === false) {
            return $this->render(500, $pay->getError());
        }
        return $this->render(200, "OK", $result->url);
    }

    function notity(): string
    {
        $config = new PayConfig(Config::getConfig("shop"));
        $pay = new Vpay($config);
        $result = $pay->payNotify(function (PayNotifyObject $notifyObject) {
            $data = Json::decode($notifyObject->param, true);
            $mail = $data['mail'];
            $item = ShopDao::getInstance()->getById($data['id']);
            $hook = $item->api;
            unset($data['item']);
            $title = Config::getConfig("shop")['title'];
            $app = AppDao::getInstance()->getByAppId(Config::getConfig("shop")['appid']);
            if (empty($app)) {
                Log::record("Notify", "回调失败，目标应用不存在");
                return;
            }

                try {
                    $return = HttpClient::init($hook)->post($data, 'form')->setHeaders(['sign' => $this->sign($data, $hook)])->send();
                    $json = Json::decode($return->getBody(), true);
                    if (isset($json['code'])) {
                        if ($json['code'] == 200) {
                            $file = AnkioMail::compileNotify("#1abc9c", "#fff", $app->app_image, $title, "购买{$item->item}成功", "<p>{$json['data']}</p>");
                            AnkioMail::send($mail, "购买{$item->item}成功", $file, $title);
                            return;
                        } else {
                            $error = $json['msg'];
                        }
                    } else {
                        $error = "接口响应错误：" . $return->getBody();
                    }
                } catch (HttpException $e) {
                    $error = $e->getMessage();
                    Log::record("Notify", "回调响应异常：" . $e->getMessage());
                }

                $file = AnkioMail::compileNotify("#df3b3b", "#fff", $app->app_image, $title, "购买{$item->item}失败", "<p>您购买的{$item->item}出现异常，请等待商家处理。</p>");
                AnkioMail::send($mail, "购买{$item->item}失败", $file, $title);
                $file = AnkioMail::compileNotify("#df3b3b", "#fff", $app->app_image, $title, "购买{$item->item}失败", "<p>用户购买的{$item->item}出现异常，请及时处理。{$error}</p><p>订单：{$notifyObject->order_id}<span></p><p>商户：{$notifyObject->app_name}</p><p>商品：{$notifyObject->app_item}</p><p>支付金额：{$notifyObject->real_price}</p><p>应付金额：{$notifyObject->price}</p><p>支付方式：" . $this->getPayType($notifyObject->pay_type) . "</p><p>支付时间：" . date("Y-m-d H:i:s", $notifyObject->pay_time) . "</p><p>携带参数：" . json_encode(json_decode($notifyObject->param) . JSON_UNESCAPED_UNICODE) . "</p>");
                AnkioMail::send($mail, "用户购买{$item->item}失败", $file, $title);

        });
        if ($result) {
            App::exit("回调成功退出");
        }
        return $this->render(500, $pay->getError());
    }

    private function sign($array, $key): string
    {
        ksort($array);
        return hash_hmac('sha256', http_build_query($array), $key);
    }

    private function getPayType(int $pay_type): string
    {
        return match ($pay_type){
            OrderModel::PAY_ALIPAY=>'支付宝（实时）',
            OrderModel::PAY_ALIPAY_APP=>"支付宝（24小时）",
            OrderModel::PAY_WECHAT_APP=>"微信（24小时）",
            default => ""
        };
    }
}
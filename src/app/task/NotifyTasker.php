<?php
/*
 * Copyright (c) 2023. Ankio.  由CleanPHP4强力驱动。
 */

/**
 * Package: app\task
 * Class NotifyTasker
 * Created By ankio.
 * Date : 2023/5/15
 * Time : 15:01
 * Description :
 */

namespace app\task;


use app\database\dao\AppDao;
use app\database\dao\OrderDao;
use app\database\model\OrderModel;
use cleanphp\App;
use cleanphp\base\Config;
use cleanphp\base\Variables;
use cleanphp\cache\Cache;
use cleanphp\file\Log;
use library\http\HttpClient;
use library\http\HttpException;
use library\login\SignUtils;
use library\mail\AnkioMail;
use library\task\TaskerAbstract;
use library\task\TaskerManager;
use library\task\TaskerTime;
use Throwable;

class NotifyTasker extends TaskerAbstract
{

    private OrderModel $order;
    private string $key;

    public function __construct($order, $key)
    {
        $this->order = $order;
        $this->key = $key;
    }

    /**
     * @inheritDoc
     */
    public function getTimeOut(): int
    {
        return 600;//理论上时间不会超过5分钟
    }

    /**
     * @inheritDoc
     */
    public function onStart()
    {
       $order = OrderDao::getInstance()->getByOrderId($this->order->order_id);
       if (empty($order) || $order->state === OrderModel::SUCCESS){
          App::$debug && Log::record("Notify","该订单回调成功不再处理。");
           Cache::init()->del($this->order->order_id . "_fail");
           return;
       }
       $app = AppDao::getInstance()->getByAppId($order->appid);
       if(empty($app)){
           App::$debug && Log::record("Notify","该订单对应App不存在。");
           Cache::init()->del($this->order->order_id . "_fail");
           return;
       }
        $array = $this->order->toArray();
        $array['t'] = time();
        $array = SignUtils::sign($array, $this->key);
        try {
            $http = HttpClient::init($this->order->notify_url)->post($array,'form')->send('/');
            if ($http->getBody() !== "success") {
                Cache::init(3600*24*15,Variables::getCachePath("notify",DS))->set($this->order->order_id . "_fail_msg",$http->getBody());

                throw new HttpException("回调接口没有输出 success 字符");
            }
            $this->order->state = OrderModel::SUCCESS;
            OrderDao::getInstance()->updateModel($this->order);
            if (Config::getConfig("notice")['success_notice']) {
                $file = AnkioMail::compileNotify("#1abc9c", "#fff", $app->app_image, $app->app_name, "用户支付成功通知", "<p>订单{$this->order->order_id}支付成功<span></p><p>商户：{$this->order->app_name}</p><p>商品：{$this->order->app_item}</p><p>支付金额：{$this->order->real_price}</p><p>应付金额：{$this->order->price}</p><p>支付方式：" . $this->getPayType($this->order->pay_type) . "</p><p>支付时间：" . date("Y-m-d H:i:s", $this->order->pay_time) . "</p><p>携带参数：" . json_encode(json_decode($this->order->param) , JSON_PRETTY_PRINT) . "</p>");
                AnkioMail::send(Config::getConfig("mail")['received'], "用户支付成功通知", $file, $app->app_name);
            }

            Cache::init()->del($this->order->order_id . "_fail");

        } catch (HttpException $e) {
           Log::record("Notify", "回调失败：" . $e->getMessage());
            $time = Cache::init()->get($this->order->order_id . "_fail");
            if (empty($time)) $time = 0;
            //4m、10m、10m、1h、2h、6h、15h
            switch ($time) {//类似于支付宝，回调通知失败后重新回调
                case 0:
                    $next = TaskerTime::nMinute(4);
                    break;
                case 1:
                case 2:
                    $next = TaskerTime::nMinute(10);
                    break;
                case 3:
                    $next = TaskerTime::nHour(1,0);
                    break;
                case 4:
                    $next = TaskerTime::nHour(2,0);
                    break;
                case 5:
                    $next = TaskerTime::nHour(6,0);
                    break;
                case 6:
                    $next = TaskerTime::nHour(15,0);
                    break;
                default:
                    Log::record("Notify", "多次回调失败不再尝试回调：" . $e->getMessage());


                    Cache::init()->del($this->order->order_id . "_fail");
                    $file = AnkioMail::compileNotify("#e74c3c", "#fff",  $app->app_image, $app->app_name, "异步回调失败", "<p>订单{$this->order->order_id}异步回调失败<span></p><p>商户：{$this->order->app_name}</p><p>商品：{$this->order->app_item}</p><p>支付金额：{$this->order->real_price}</p><p>应付金额：{$this->order->price}</p><p>支付方式：" . $this->getPayType($this->order->pay_type) . "</p><p>支付时间：" . date("Y-m-d H:i:s", $this->order->pay_time) . "</p><p>携带参数：" . json_encode(json_decode($this->order->param) . JSON_UNESCAPED_UNICODE) . "</p>");

                    AnkioMail::send(Config::getConfig("mail")['received'], "异步回调失败", $file, $app->app_name);

                    return;
            }
            Cache::init()->set($this->order->order_id . "_fail", ++$time);
            //处理失败的定时任务

                TaskerManager::del("异步回调任务_" . $this->order->order_id);
            TaskerManager::add($next, new NotifyTasker($this->order, $this->key), "异步回调任务_" . $this->order->order_id);
        }

    }

    private function getPayType($type): string
    {
        switch ($type) {
            case OrderModel::PAY_ALIPAY:
                return "支付宝";
            default:
                return "微信";
        }
    }

    /**
     * @inheritDoc
     */
    public function onStop()
    {

    }

    /**
     * @inheritDoc
     */
    public function onAbort(Throwable $e)
    {

    }
}
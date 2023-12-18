<?php
/*
 * Copyright (c) 2023. Ankio.  由CleanPHP4强力驱动。
 */

namespace app\channels;

use app\database\dao\OrderDao;
use app\database\model\OrderModel;
use app\exception\ChannelException;
use app\objects\config\AppConfig;
use cleanphp\base\Config;
use cleanphp\cache\Cache;

class AppChannel
{
    const CONFLICT_INCREASE = 1;
    const CONFLICT_REDUCE = 2;

    /**
     * @throws ChannelException
     */
    public function create(OrderModel $order): OrderModel
    {
        if(!self::isActive() && Config::getConfig("app")['app_key']!==""){
            throw new ChannelException("收款设备不在线");
        }
        $app = new AppConfig(Config::getConfig("app"));
        $order->order_id = uniqid("pay_") . rand(1000, 9999);
        $order->create_time = time();
        if ($order->pay_type == OrderModel::PAY_WECHAT_APP) {
            $order->pay_image = $app->app_wechat;
        } elseif ($order->pay_type == OrderModel::PAY_ALIPAY_APP) {
            $order->pay_image = $app->app_alipay;
        } else {
            throw new ChannelException("不支持的支付方式");
        }
        return $this->getPayMoney($order, $app->app_conflict);
    }

    /**
     * @throws ChannelException
     */
    protected function getPayMoney(OrderModel $order, int $onDuplicate): OrderModel
    {
        $reallyPrice = intval(bcmul($order->price, 100));

        $find = false;
        for ($i = 0; $i < 10; $i++) {
            $result = OrderDao::getInstance()->getWaitOrderByPayType($order->pay_type, bcdiv($reallyPrice, 100, 2));
            if (empty($result)) {
                $find = true;
                break;
            }
            if ($onDuplicate === self::CONFLICT_INCREASE) {
                $reallyPrice++;
            } else {
                $reallyPrice--;
            }

        }
        if ($reallyPrice <= 0 || !$find) {
            throw new ChannelException("该时间段订单量过大，请换个时间或者换个支付方式重试");
        }
        $order->real_price = bcdiv($reallyPrice, 100, 2);
        return $order;
    }
    static function isActive(): bool
    {
        return !empty(Cache::init(900)->get("last_heart"));
    }
}
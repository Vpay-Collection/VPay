<?php
/*
 * Copyright (c) 2023. Ankio.  由CleanPHP4强力驱动。
 */

/**
 * Package: app\utils\channel
 * Class ServerChannel
 * Created By ankio.
 * Date : 2023/3/13
 * Time : 14:50
 * Description :
 */

namespace app\utils;

use app\database\dao\OrderDao;
use app\database\model\OrderModel;
use app\exception\ChannelException;
use cleanphp\base\Config;
use cleanphp\cache\Cache;


class AppChannel
{
    const CONFLICT_INCREASE = 1;
    const CONFLICT_REDUCE = 2;


    /**
     * 从该渠道创建订单
     * @return OrderModel
     * @throws ChannelException
     * @var OrderModel $order 预先创建的订单信息
     */
    public function create(OrderModel $order): OrderModel
    {
        if (!$this->isActive())
            throw new ChannelException("App收款渠道暂时不可用");
        $order->order_id = uniqid("pay_") . rand(1000, 9999);
        $order->create_time = time();
        if ($order->pay_type == OrderModel::PAY_WECHAT) {
            $order->pay_image = Config::getConfig("channel")['wechat'];
        } elseif ($order->pay_type == OrderModel::PAY_ALIPAY) {
            $order->pay_image = Config::getConfig("channel")['alipay'];
        } else {
            throw new ChannelException("不支持的支付方式");
        }
        return $this->getPayMoney($order, Config::getConfig("app")['conflict']);
    }

    /**
     * @param OrderModel $order
     * @param int $onDuplicate
     * @throws ChannelException
     */
    protected function getPayMoney(OrderModel $order, int $onDuplicate): OrderModel
    {
        //将订单金额转为整数
        $reallyPrice = intval(bcmul($order->price, 100));
        $find = false;
        for ($i = 0; $i < 10; $i++) {
            $result = OrderDao::getInstance()->getWaitOrderByPayType($order->pay_type, bcdiv($reallyPrice, 100, 2));
            if (empty($result)) {
                $find = true;
                //不存在既往订单，就是找到了
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
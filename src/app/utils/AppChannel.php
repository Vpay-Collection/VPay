<?php
/*******************************************************************************
 * Copyright (c) 2022. Ankio. All Rights Reserved.
 ******************************************************************************/

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
    protected int $type = 0; //收款渠道

    public function __construct($type)
    {
        $this->type = $type;
    }


    /**
     * 从该渠道创建订单
     * @return void
     * @throws ChannelException
     * @var OrderModel $order 预先创建的订单信息
     */
    public function create(OrderModel &$order)
    {
        if (!$this->isActive())
            throw new ChannelException("App收款渠道暂时不可用");
        $order->order_id = uniqid("pay_") . rand(1000, 9999);
        $order->create_time = time();
        $order->pay_type = $this->type;
        if ($this->type == OrderModel::PAY_WECHAT) {
            $order->pay_image = Config::getConfig("channel")['wechat'];
        } elseif ($this->type == OrderModel::PAY_ALIPAY) {
            $order->pay_image = Config::getConfig("channel")['alipay'];
        } elseif ($this->type == OrderModel::PAY_QQ) {
            $order->pay_image = Config::getConfig("channel")['qq'];
        } else {
            throw new ChannelException("不支持的支付方式");
        }
        $this->getPayMoney($order, Config::getConfig("app")['conflict'], Config::getConfig("app")['timeout']);
    }

    /**
     * @param OrderModel $order
     * @param int $onDuplicate
     * @param int $timeout
     * @throws ChannelException
     */
    protected function getPayMoney(OrderModel &$order, int $onDuplicate, int $timeout)
    {
        $timeout = $order->create_time - $timeout * 60;//当前时间往前推
        //将订单金额转为整数
        $reallyPrice = intval(bcmul($order->price, 100));
        $price = $order->price;
        for ($i = 0; $i < 10; $i++) {
            $result = OrderDao::getInstance()->getWaitOrderByPayType($order->pay_type, $price);
            if (empty($result)) {
                //不存在既往订单，就是找到了
                break;
            }
            if ($onDuplicate === self::CONFLICT_INCREASE) {
                $reallyPrice++;
            } else {
                $reallyPrice--;
            }
        }
        if ($reallyPrice <= 0) {
            throw new ChannelException("该时间段订单量过大，请换个时间尝试重试");
        }
        $order->real_price = bcdiv($reallyPrice, 100, 2);
    }

    static function isActive(): bool
    {
        return !empty(Cache::init(600)->get("last_heart"));
    }
}
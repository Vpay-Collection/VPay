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

use app\database\model\OrderModel;
use app\exception\ChannelException;
use cleanphp\base\Config;


class WechatChannel
{



    /**
     * 从该渠道创建订单
     * @return OrderModel
     * @throws ChannelException
     * @var OrderModel $order 预先创建的订单信息
     */
    public function create(OrderModel $order): OrderModel
    {
        if (!$this->isActive())
            throw new ChannelException("支付宝收款渠道暂时不可用");

        return $order;
    }
    

    static function isActive(): bool
    {
        return !empty(Config::getConfig("wechat"));
    }
}
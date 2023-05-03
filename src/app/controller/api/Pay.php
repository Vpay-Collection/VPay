<?php
/*******************************************************************************
 * Copyright (c) 2022. Ankio. All Rights Reserved.
 ******************************************************************************/

/**
 * Package: app\controller\api
 * Class Pay
 * Created By ankio.
 * Date : 2023/3/19
 * Time : 10:55
 * Description :
 */

namespace app\controller\api;

use app\exception\ChannelException;
use app\objects\CreateOrderObject;
use app\utils\ChannelSelector;
use library\verity\VerityException;

class Pay extends BaseController
{
    /**
     * 创建订单
     * @return string
     */
    public function CreateOrder(): string
    {
        try {
            $orderObject = new CreateOrderObject(arg());
        } catch (VerityException $e) {
            return $this->json(self::API_ERROR, $e->getMessage());
        }
        //通过验证校验后，将订单转化为Order
        $order = $orderObject->toOrder();
        //选择可用支付渠道创建订单
        try {
            ChannelSelector::getChannel($order->pay_type)->create($order);
        } catch (ChannelException $e) {
            return $this->json(self::API_ERROR, $e->getMessage());
        }
        return $this->json(self::API_SUCCESS, null, $order->toArray());
    }

    /**
     * 检查订单状态
     */

}
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

namespace app\channels;

use app\database\model\OrderModel;
use app\exception\ChannelException;
use app\objects\config\AlipayConfig;
use app\utils\alipay\AlipayServiceSend;
use cleanphp\base\Config;


class AlipayChannel
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
        $order->order_id = date("YmdHis") . rand(10000, 99999);
        $order->create_time = time();
        $pay = new AlipayConfig(Config::getConfig("alipay"));
        $appid = $pay->alipay_id;
        $saPrivateKey = $pay->alipay_private_key;
        $timeout = $pay->validity_minute;
        $aliPay = new AlipayServiceSend($appid, $saPrivateKey);
        $result = $aliPay->doPay($order->price, $order->order_id, $order->app_item, url("api","notify","alipay"),$timeout);
        $result = $result['alipay_trade_precreate_response'];
        $order->real_price = $order->price;
        if (isset($result['code']) && $result['code'] == '10000') {
            $order->pay_image = url("api","image","qrcode",['url'=>$result['qr_code']]);
        }
        return $order;
    }


    static function isActive(): bool
    {
        return !empty(Config::getConfig("alipay"));
    }
}
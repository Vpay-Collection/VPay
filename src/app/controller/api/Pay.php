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

use app\database\dao\OrderDao;
use app\database\model\OrderModel;
use app\exception\ChannelException;
use app\objects\order\CreateOrderObject;
use app\objects\order\OrderObject;
use app\utils\AppChannel;
use cleanphp\base\Config;
use cleanphp\base\Session;
use library\login\SignUtils;
use library\verity\VerityException;

class Pay extends BaseController
{
    /**
     * 创建订单
     * @return string
     */
    public function create(): string
    {
        try {
            $orderObject = new CreateOrderObject(arg());
        } catch (VerityException $e) {
            return $this->json(self::API_ERROR, $e->getMessage());
        }
        //通过验证校验后，将订单转化为Order
        $order = $orderObject->toOrder();
        $order->state = OrderModel::WAIT;
        //选择可用支付渠道创建订单
        try {
            (new  AppChannel($orderObject->pay_type))->create($order);
            OrderDao::getInstance()->insertModel($order);
        } catch (ChannelException $e) {
            return $this->json(self::API_ERROR, $e->getMessage());
        }
        $return = [
            'order_id' => $order->order_id,
            'url' => url("index", "main", "pay", ["id" => $order->order_id]),
            'pay_image' => $order->pay_image,
            'create_time' => $order->create_time,
            'app_item' => $order->app_item,
            'app_name' => $order->app_name,
            'real_price' => $order->real_price,
            'price' => $order->price,
            'param' => $order->param,
        ];

        return $this->json(self::API_SUCCESS, null, $return);
    }

    /**
     * 检查订单状态
     */

    function payState(): string
    {
        $order = Session::getInstance()->get("order_id");
        $result = OrderDao::getInstance()->getByOrderIdNoFilter($order);
        if (empty($result)) {
            return $this->json(self::API_ERROR, "订单不存在");
        }
        return $this->json(self::API_SUCCESS, null, [
            'state' => $result->state,
            'return_url' => $result->state === OrderModel::PAID ? $this->getReturnUrl($result) : ""
        ]);
    }

    /**
     * 关闭订单
     * @return string
     */
    function close(): string
    {
        try {
            $orderObject = new OrderObject(arg());
        } catch (VerityException $e) {
            return $this->json(self::API_ERROR, $e->getMessage());
        }
        OrderDao::getInstance()->closeOrder($orderObject->order_id, $orderObject->appid);
        return $this->json(200);
    }

    /**
     * 查询订单状态
     * @return string
     */
    function status(): string
    {
        try {
            $orderObject = new OrderObject(arg());
        } catch (VerityException $e) {
            return $this->json(self::API_ERROR, $e->getMessage());
        }
        return $this->json(self::API_SUCCESS, null, [
            'state' => $orderObject->order->state,
            'return_url' => $orderObject->order->state === OrderModel::PAID ? $this->getReturnUrl($orderObject->order) : ""
        ]);
    }

    /**
     * 查询订单信息
     * @return string
     */
    function order(): string
    {
        try {
            $orderObject = new OrderObject(arg());
        } catch (VerityException $e) {
            return $this->json(self::API_ERROR, $e->getMessage());
        }
        return $this->json(self::API_SUCCESS, null, $orderObject->order);
    }

    /**
     * 获取同步回调的URL
     * @param OrderModel $orderModel
     * @return string
     */
    private function getReturnUrl(OrderModel $orderModel): string
    {
        $url = parse_url($orderModel->return_url);
        if (!isset($url['scheme']) || !isset($url['host'])) return $orderModel->return_url;
        $array = [
            'order_id' => $orderModel->order_id,
            'param' => $orderModel->param,
            'real_price' => $orderModel->real_price,
            'price' => $orderModel->price,
            'app_item' => $orderModel->app_item
        ];


        if (isset($url['query'])) {
            parse_str($url['query'], $result);
            $array = array_merge($result, $array);
        }
        $array['t'] = time();
        $array = SignUtils::sign($array, Config::getConfig('app')['key']);
        return $url['scheme'] . "://" . $url['host'] . "?" . http_build_query($array);
    }


}
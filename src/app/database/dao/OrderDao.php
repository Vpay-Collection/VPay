<?php
/*******************************************************************************
 * Copyright (c) 2022. Ankio. All Rights Reserved.
 ******************************************************************************/

/**
 * Package: app\database\dao
 * Class OrderDao
 * Created By ankio.
 * Date : 2023/3/13
 * Time : 13:58
 * Description : 订单操作类
 */

namespace app\database\dao;

use app\database\model\OrderModel;
use library\database\exception\DbFieldError;
use library\database\object\Dao;
use library\database\object\Field;
use library\database\operation\SelectOperation;


class OrderDao extends Dao
{
    public function __construct()
    {
        parent::__construct(OrderModel::class);
    }

    /**
     * @inheritDoc
     */
    protected function getTable(): string
    {
        return 'order';
    }

    /**
     * 关闭超时订单
     * @return void
     */
    public function closeTimeoutOrder()
    {
        $close_time = Config::getConfig("pay")['timeout'];
        $close_time = time() - 60 * $close_time;//计算订单关闭时间
        $close_date = time();
        $this->update()
            ->where(["create_time <= $close_time and state = :state", ":state" => OrderModel::WAIT])
            ->set(["state" => OrderModel::CLOSE, "close_time" => $close_date])
            ->commit();
    }

    /**
     * 获取最近订单
     * @param int $count
     * @return array|int
     * @throws DbFieldError
     */
    public function getRecently(int $count = 5)
    {
        return $this->select()->where(['state' => OrderModel::SUCCESS])->limit($count)->orderBy("id", SelectOperation::SORT_ASC)->commit();
    }

    /**
     * 获取收入总金额
     * @return int|mixed
     */
    public function getTotal()
    {
        return $this->getSum(['state' => OrderModel::SUCCESS], 'real_price');
    }

    /**
     * 获取当日总收入
     * @return int|mixed
     */
    public function getToday()
    {
        return $this->getSum(['state' => OrderModel::SUCCESS, 'close_time>:time', ':time' => strtotime(date('Y-m-d', time()))], 'real_price');
    }


    /**
     * 支付成功回调
     * @param OrderModel $model
     * @return void
     */
    function callback(OrderModel $model)
    {
        //订单支付成功回调
        $model->state = OrderModel::PAID;
        $this->updateModel($model);

    }

    /**
     * 通知服务器支付成功
     * @param $order_id
     * @return void
     * @throws OrderNotFoundException
     */
    public function notify($order_id)
    {
        $model = $this->find(null, ['order_id' => $order_id]);
        if (empty($model)) {
            throw new OrderNotFoundException("找不到订单信息");
        }
        //不要阻塞当前进程
        go(function () use ($model) {
            $this->callback($model);
        });
    }

    /**
     * 根据服务端支付方式获取订单数
     * @param $server_type int 服务端支付方式支持{@link OrderModel::APP_ALIPAY}(App监控的支付宝)/{@link OrderModel::APP_WECHAT}(App监控的微信)/{@link OrderModel::OFFICIAL_ALIPAY}(官方渠道的支付宝)
     * @param int $price
     * @return array|int
     */
    public function getWaitOrderByPayType(int $server_type, int $price = 0): ?OrderModel
    {
        $condition = [];
        if ($price !== 0) {
            $condition['price'] = $price;
        }
        $timeout = Config::getConfig('pay')['timeout'];
        $condition[] = "create_time > " . (time() - $timeout);
        $condition['server_type'] = $server_type;
        $condition['state'] = OrderModel::WAIT;
        return $this->find(null, $condition);
    }


}
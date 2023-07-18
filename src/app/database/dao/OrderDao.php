<?php
/*
 * Copyright (c) 2023. Ankio.  由CleanPHP4强力驱动。
 */

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
use app\exception\OrderNotFoundException;
use app\task\NotifyTasker;
use cleanphp\base\Config;
use library\database\exception\DbFieldError;
use library\database\object\Dao;
use library\database\operation\SelectOperation;
use library\task\TaskerManager;
use library\task\TaskerTime;


class OrderDao extends Dao
{


    /**
     * 关闭超时订单
     * @return void
     */
    public function closeTimeoutOrder(): void
    {
        $close_time = Config::getConfig("app")['timeout'];
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
     * @return array|null
     * @throws DbFieldError
     */
    public function getRecently(int $count = 5): ?array
    {
        return $this->select()->where(['state' => OrderModel::SUCCESS])->limit($count)->orderBy("id", SelectOperation::SORT_ASC)->commit(false);
    }

    /**
     * 统计数据
     * @return array
     */
    public function countData(): array
    {
        $start = strtotime(date('Y-m-d', strtotime("-0 day")));
        $end = time();
        $result = [];
        $day = [];
        for ($i = 1; $i < 8; $i++) {
            $day[] = date('Y-m-d', $start);
            $result[] = $this->getSum(["close_time>$start", "close_time<$end", 'state' => OrderModel::SUCCESS], 'real_price');
            $end = $start;
            $start = strtotime(date('Y-m-d', strtotime("-$i day")));
        }

        return [$day, $result];
    }

    /**
     * 获取收入总金额
     * @return string
     */
    public function getTotal(): string
    {
        return $this->getSum(['state' => OrderModel::SUCCESS], 'real_price');
    }

    /**
     * 获取当日总收入
     * @return string
     */
    public function getToday(): string
    {
        return $this->getSum(['state' => OrderModel::SUCCESS, 'close_time>:time', ':time' => strtotime(date('Y-m-d', time()))], 'real_price');
    }


    /**
     * 通知服务器支付成功
     * @param $order_id
     * @param $key
     * @return void
     * @throws OrderNotFoundException
     */
    public function notify($order_id, $key): void
    {
        /**@var $model OrderModel */
        $model = $this->find(null, ['order_id' => $order_id]);
        if (empty($model)) {
            throw new OrderNotFoundException("找不到订单信息");
        }
        $model->pay_time = time();
        $model->state = OrderModel::PAID;
        $model->close_time = time();
        $this->updateModel($model);
        TaskerManager::del("异步回调任务_" .$model->order_id);
        TaskerManager::add(TaskerTime::nMinute(0), new NotifyTasker($model, $key), "异步回调任务_" . $model->order_id);
        //不要阻塞当前进程
    }

    /**
     * 根据PAY_TYPE支付方式获取订单数
     * @param $pay_type int 服务端支付方式支持{@link OrderModel::PAY_ALIPAY}(App监控的支付宝)/{@link OrderModel::PAY_WECHAT}(App监控的微信)/{@link OrderModel::PAY_QQ}(App监控的QQ)
     * @param float $price
     * @return OrderModel|null
     */
    public function getWaitOrderByPayType(int $pay_type, float $price = 0): ?OrderModel
    {
        $this->closeTimeoutOrder();
        $condition = [
            'real_price' => $price,
            'pay_type' => $pay_type,
            'state' => OrderModel::WAIT
        ];
        return $this->find(null, $condition);
    }

    public function getByOrderIdWait($id): ?OrderModel
    {
        $this->closeTimeoutOrder();
        return $this->find(null, ["order_id" => $id, "state" => OrderModel::WAIT]);
    }

    public function getByOrderId($id): ?OrderModel
    {
        $this->closeTimeoutOrder();
        return $this->find(null, ["order_id" => $id]);
    }

    public function getByOrderIdNoFilter($id): ?OrderModel
    {
        $this->closeTimeoutOrder();
        return $this->find(null, ["order_id" => $id]);
    }

    public function closeOrder($order, $app): void
    {
        $this->update()->where(['order_id' => $order, 'appid' => $app])->commit();
    }

    public function getOrderByApp($order, $app): ?OrderModel
    {
        return $this->find(null, ['order_id' => $order, 'appid' => $app]);
    }

    public function delByAppid($id): void
    {
        $this->delete()->where(['appid' => $id])->commit();
    }

}
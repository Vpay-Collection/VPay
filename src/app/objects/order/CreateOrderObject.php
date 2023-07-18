<?php
/*
 * Copyright (c) 2023. Ankio.  由CleanPHP4强力驱动。
 */

/**
 * Package: app\objects
 * Class CreateOrderObject
 * Created By ankio.
 * Date : 2023/3/19
 * Time : 10:59
 * Description :
 */

namespace app\objects\order;

use app\database\model\OrderModel;
use cleanphp\base\Config;
use library\verity\VerityException;
use library\verity\VerityRule;

class CreateOrderObject extends BaseSignObject
{
    public int $pay_type = OrderModel::PAY_WECHAT;//支付类型
    public string $app_item = "";//商户商品
    public string $notify_url = "";//异步通知链接
    public string $return_url = "";//异步通知链接
    public float $price = 0.00;//支付金额

    public string $param = "{}";//其他参数

    public string $app_name = "";

    public string $pay_image = "";

    public function __construct(array $item = [])
    {
        parent::__construct($item);
        $this->pay_image = "";
        if ($this->pay_type === OrderModel::PAY_ALIPAY) {
            $this->pay_image = Config::getConfig("channel")["alipay"];
        } elseif ($this->pay_type === OrderModel::PAY_WECHAT) {
            $this->pay_image = Config::getConfig("channel")["wechat"];
        } else {
            throw new VerityException("不支持当前支付渠道", "pay_type", $this->pay_type);
        }
        if (empty($this->pay_image)) {
            throw new VerityException("当前收款渠道未配置收款码，请等待站长配置后再试", "pay_type", $this->pay_type);
        }
        $this->app_name = $this->appModel->app_name;
    }

    /**
     * 转化为订单类
     * @return OrderModel
     */
    function toOrder(): OrderModel
    {
        return new OrderModel($this->toArray());
    }

    function getRules(): array
    {
        return array_merge(parent::getRules(), [
            'pay_type' => new VerityRule("^1|2|3$", "请传输收款信息", false),
            'notify_url' => new VerityRule('', "请传入异步通知链接", false),
            'return_url' => new VerityRule('', "请传入同步通知链接", false),
            'price' => new VerityRule(VerityRule::FLOAT_AND_INT, '金额必须为浮点数', false)
        ]);
    }

}
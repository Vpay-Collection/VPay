<?php
/*******************************************************************************
 * Copyright (c) 2022. Ankio. All Rights Reserved.
 ******************************************************************************/

/**
 * Package: app\objects
 * Class CreateOrderObject
 * Created By ankio.
 * Date : 2023/3/19
 * Time : 10:59
 * Description :
 */

namespace app\objects;

use app\database\model\OrderModel;
use library\verity\VerityRule;

class CreateOrderObject extends BaseSignObject
{
    public int $pay_type = OrderModel::PAY_WECHAT;//支付类型
    public string $app_item = "";//商户商品
    public string $notify_url = "";//异步通知链接
    public string $return_url = "";//异步通知链接
    public float $price = 0.00;//支付金额

    public string $param = "{}";//其他参数

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
            'pay_type' => new VerityRule("^0|1$", "支付类型错误", false),
            'app_item' => new VerityRule('', "请传输收款信息", false),
            'notify_url' => new VerityRule('', "请传入异步通知链接", false),
            'return_url' => new VerityRule('', "请传入同步通知链接", false),
            'price' => new VerityRule(VerityRule::FLOAT_AND_INT, '金额必须为浮点数', false)
        ]);
    }

}
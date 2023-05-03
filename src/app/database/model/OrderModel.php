<?php
/*******************************************************************************
 * Copyright (c) 2022. Ankio. All Rights Reserved.
 ******************************************************************************/

/**
 * Package: app\database\model
 * Class OrderModel
 * Created By ankio.
 * Date : 2023/3/9
 * Time : 12:26
 * Description :
 */

namespace app\database\model;

use app\database\dao\OrderDao;
use library\database\object\Model;
use library\database\object\SqlKey;

class OrderModel extends Model
{
    const SUCCESS = 3;//订单成功
    const PAID = 2;//已支付
    const WAIT = 1;//等待支付
    const CLOSE = -1;//已关闭

    const PAY_WECHAT = 0;
    const PAY_ALIPAY = 1;


    const APP_WECHAT = 3;
    const APP_ALIPAY = 4;

    const OFFICIAL_ALIPAY = 5;

    public int $id = 0;//唯一id
    public int $pay_type = 0;//支付类型
    public int $server_type = 0;//服务端监听类型
    public float $price = 0.00;//支付金额
    public string $remark = "";//需要用户的备注信息
    public float $real_price = 0.00;//真实支付的金额
    public string $app_name = "";//商户，商户名称
    public string $app_item = "";//商户商品
    public string $app_id = "";//appid
    public string $order_id = "";//订单id
    public string $notify_url = "";//异步通知链接
    public string $return_url = "";//异步通知链接
    public int $create_time = 0;//时间戳
    public int $pay_time = 0;//时间戳
    public int $close_time = 0;//时间戳
    public int $state = 0;//订单状态

    public string $param = "";//附加参数

    public string $pay_image = "";//支付二维码

    function getPrimaryKey(): SqlKey
    {
        return new SqlKey('id', 0, true);
    }


}
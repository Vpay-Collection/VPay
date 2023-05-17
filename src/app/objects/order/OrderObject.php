<?php
/*******************************************************************************
 * Copyright (c) 2022. Ankio. All Rights Reserved.
 ******************************************************************************/

/**
 * Package: app\objects\order
 * Class OrderObject
 * Created By ankio.
 * Date : 2023/5/15
 * Time : 14:40
 * Description :
 */

namespace app\objects\order;

use app\database\dao\OrderDao;
use app\database\model\OrderModel;
use library\verity\VerityException;

class OrderObject extends BaseSignObject
{
    public string $order_id = "";
    public ?OrderModel $order = null;

    public function __construct(array $item = [])
    {
        parent::__construct($item);
        $this->order = OrderDao::getInstance()->getOrderByApp($this->order_id, $this->appid);
        if (empty($this->order)) throw new VerityException('订单不存在');
    }

    public function getKey(): string
    {
        return $this->appModel->app_key;
    }
}
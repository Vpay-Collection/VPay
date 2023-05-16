<?php
/*******************************************************************************
 * Copyright (c) 2022. Ankio. All Rights Reserved.
 ******************************************************************************/

/**
 * Package: app\controller\api_admin
 * Class Order
 * Created By ankio.
 * Date : 2023/5/6
 * Time : 14:50
 * Description :
 */

namespace app\controller\api_admin;

use app\database\dao\AppDao;
use app\database\dao\OrderDao;
use app\exception\OrderNotFoundException;
use library\database\object\Page;

class Order extends BaseController
{
    function list(): string
    {
        $page = new Page();

        $condition = [];
        if (!empty(arg("app_id"))) {
            $condition['app_id'] = arg("app_id", 0);
        }
        if (!empty(arg("status"))) {
            $condition['status'] = arg("status", -1);
        }
        if (!empty(arg("app_item"))) {
            $condition[] = "app_item like %:app_item%";
            $condition['app_item'] = arg("app_item");
        }

        $result = OrderDao::getInstance()->getAll([], $condition, arg("page", 1), arg("size", 10), $page);
        return $this->render(200, null, $result, $page->total_count);
    }

    function callback()
    {
        $id = arg("order_id");
        $order = OrderDao::getInstance()->getByOrderId($id);
        if (empty($order)) {
            return $this->render(404, null, "无订单");
        }
        $app = AppDao::getInstance()->getByAppId($order->appid);
        if (empty($app)) {
            return $this->render(404, null, "商户不存在");
        }
        try {
            OrderDao::getInstance()->notify($id, $app->app_key);
        } catch (OrderNotFoundException $e) {
            return $this->render(404, null, "无订单");
        }
        return $this->render(200, null, "后台回调中");
    }
}
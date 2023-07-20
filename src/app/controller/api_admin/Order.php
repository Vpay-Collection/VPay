<?php
/*
 * Copyright (c) 2023. Ankio.  由CleanPHP4强力驱动。
 */

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
        if (!empty(arg("appid"))) {
            $condition['appid'] = arg("appid", 0);
        }
        if (!empty(arg("status"))) {
            $condition['state'] = arg("status", -1);
        }
        if (!empty(arg("app_item"))) {
            $condition[] = "app_item like %:app_item%";
            $condition['app_item'] = arg("app_item");
        }

        $result = OrderDao::getInstance()->getAll([], $condition,false, arg("page", 1), arg("size", 10), $page);
        return $this->render(200, null, $result, $page->total_count);
    }

    function callback(): string
    {
        $id = arg("order_id");
        $order = OrderDao::getInstance()->getByOrderId($id);
        if (empty($order)) {
            return $this->render(404, "无订单");
        }
        $app = AppDao::getInstance()->getByAppId($order->appid);
        if (empty($app)) {
            return $this->render(404, "商户不存在");
        }
        try {
            OrderDao::getInstance()->notify($id, $app->app_key);
        } catch (OrderNotFoundException $e) {
            return $this->render(404, "无订单" . $e->getMessage());
        }
        return $this->render(200,  "后台回调中");
    }
}
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

use app\database\dao\OrderDao;
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
        //TODO 手动回调
    }
}
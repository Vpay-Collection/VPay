<?php
/*******************************************************************************
 * Copyright (c) 2022. CleanPHP. All Rights Reserved.
 ******************************************************************************/
/**
 * Class Orders
 * Created By ankio.
 * Date : 2022/1/30
 * Time : 3:35 下午
 * Description :
 */

namespace app\controller\admin;

use app\model\Order;

class Orders extends BaseController
{
    function list(): array
    {
        $app = new Order();
        $res = $app->getOrders(arg("page",1), arg("limit",15),arg("state"),arg("app"));
        if (!empty($res)) {
            $count = sizeof($res);
            if ($count === 0) return $this->ret(403,"暂无数据",$res,0);
            else return $this->ret(0,"获取成功",$res,$app->getPage()===null?$count:$app->getPage()->getTotalCount());
        } else {
            return $this->ret(403, "暂无数据", $res, 0);
        }
    }

    public function del()
    {
        $ord = new Order();
        $ord->DelOrderById(arg("id"));
        return $this->ret(200, "删除成功");

    }

    public function delGq()
    {
        $ord = new Order();
        $ord->DelOverOrder();
        return $this->ret(200, "删除成功");
    }

    public function setBD()
    {//使用异步回调接口进行补单
        $ord = new Order();
        return $ord->Notify(arg("id"));
    }



}
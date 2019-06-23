<?php

/*
 * 后台请求菜单，并且让菜单显示到主页面上来
 * */

class MenuController extends BaseController
{
    public function actionIndex()
    {//后台菜单
        $menu = array(
            array(
                "name" => "系统设置",
                "type" => "url",
                "url" => url("admin/menu", "Setting") . "?t=" . time(),
            ),
            array(
                "name" => "监控端设置",
                "type" => "url",
                "url" => url("admin/menu", "Monitor") . "?t=" . time(),
            ),
            array(
                "name" => "应用管理",
                "type" => "menu",
                "node" => array(
                    array(
                        "name" => "添加",
                        "type" => "url",
                        "url" => url("admin/menu", "AppInsert") . "?t=" . time(),
                    ),
                    array(
                        "name" => "管理",
                        "type" => "url",
                        "url" => url("admin/menu", "AppMan") . "?t=" . time(),
                    )
                ),
            ), array(
                "name" => "微信二维码",
                "type" => "menu",
                "node" => array(
                    array(
                        "name" => "添加",
                        "type" => "url",
                        "url" => url("admin/menu", "InsertWxQr") . "?t=" . time(),
                    ),
                    array(
                        "name" => "管理",
                        "type" => "url",
                        "url" => url("admin/menu", "ManaWxQr") . "?t=" . time(),
                    )
                ),
            ), array(
                "name" => "支付宝二维码",
                "type" => "menu",
                "node" => array(
                    array(
                        "name" => "添加",
                        "type" => "url",
                        "url" => url("admin/menu", "InsertAliQr") . "?t=" . time(),
                    ),
                    array(
                        "name" => "管理",
                        "type" => "url",
                        "url" => url("admin/menu", "ManaAliQr") . "?t=" . time(),
                    )
                ),
            ), array(
                "name" => "订单列表",
                "type" => "url",
                "url" => url("admin/menu", "OrderList") . "?t=" . time(),
            ), array(
                "name" => "Api说明",
                "type" => "url",
                "url" => url("admin/menu", "Api") . "?t=" . time(),
            )
        );

        echo json_encode(array("code" => 1, "data" => $menu));

    }

    public function actionSetting()
    {
        $this->display("/admin/setting.html");
    }//系统设置

    public function actionMonitor()
    {
        $this->display("/admin/jk.html");
    }//监控设置

    public function actionInsertWxQr()
    {
        $this->display("/admin/addwxqrcode.html");
    }//添加微信二维码

    public function actionManaWxQr()
    {
        $this->display("/admin/wxqrcodelist.html");
    }//管理微信二维码

    public function actionInsertAliQr()
    {
        $this->display("/admin/addzfbqrcode.html");
    }//添加支付宝二维码

    public function actionManaAliQr()
    {
        $this->display("/admin/zfbqrcodelist.html");
    }//管理支付宝二维码

    public function actionOrderList()
    {
        $this->display("/admin/orderlist.html");
    }//订单列表

    public function actionApi()
    {
        $this->host = $_SERVER["HTTP_HOST"];
        $conf = new Config();

        $lastheart = $conf->GetData(Config::lastheart);//最后心跳时间

        if ((time() - $lastheart) > 120) {
            $conf->UpdateData("jkstate", 1);//表示心跳异常
        }
        $this->display("/admin/api.html");
    }//Api说明

    public function actionMainBody()
    {
        $this->display("/admin/main.html");
    }//Api说明

    public function actionAppInsert()
    {
        $this->display("/admin/addapplication.html");
    }//添加应用

    public function actionAppMan()
    {
        $this->display("/admin/applicationlist.html");
    }//应用管理
}
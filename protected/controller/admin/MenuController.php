<?php

/*
 * 后台请求菜单，并且让菜单显示到主页面上来
 * */
namespace controller\admin;
use lib\speed\Speed;

class MenuController extends BaseController
{
    public function actionIndex()
    {//后台菜单
        $menu = array(
            array(
                "name" => "系统设置",
                "type" => "url",
                "url" => Speed::url("admin/menu", "Setting") . "?t=" . time(),
            ),
            array(
                "name" => "监控端设置",
                "type" => "url",
                "url" => Speed::url("admin/menu", "Monitor") . "?t=" . time(),
            ),
            array(
                "name" => "应用管理",
                "type" => "menu",
                "node" => array(
                    array(
                        "name" => "添加",
                        "type" => "url",
                        "url" => Speed::url("admin/menu", "AppInsert") . "?t=" . time(),
                    ),
                    array(
                        "name" => "管理",
                        "type" => "url",
                        "url" => Speed::url("admin/menu", "AppMan") . "?t=" . time(),
                    )
                ),
            ), array(
                "name" => "微信二维码",
                "type" => "menu",
                "node" => array(
                    array(
                        "name" => "添加",
                        "type" => "url",
                        "url" => Speed::url("admin/menu", "InsertWxQr") . "?t=" . time(),
                    ),
                    array(
                        "name" => "管理",
                        "type" => "url",
                        "url" => Speed::url("admin/menu", "ManaWxQr") . "?t=" . time(),
                    )
                ),
            ), array(
                "name" => "支付宝二维码",
                "type" => "menu",
                "node" => array(
                    array(
                        "name" => "添加",
                        "type" => "url",
                        "url" => Speed::url("admin/menu", "InsertAliQr") . "?t=" . time(),
                    ),
                    array(
                        "name" => "管理",
                        "type" => "url",
                        "url" => Speed::url("admin/menu", "ManaAliQr") . "?t=" . time(),
                    )
                ),
            ), array(
                "name" => "订单列表",
                "type" => "url",
                "url" =>Speed::url("admin/menu", "OrderList") . "?t=" . time(),
            ), array(
                "name" => "Api说明",
                "type" => "url",
                "url" => Speed::url("admin/menu", "Api") . "?t=" . time(),
            )
        );

        echo json_encode(array("code" => 1, "data" => $menu));

    }

    public function actionSetting()
    {
        $this->display("setting");
    }//系统设置

    public function actionMonitor()
    {
        $this->display("jk");
    }//监控设置

    public function actionInsertWxQr()
    {
        $this->display("addwxqrcode");
    }//添加微信二维码

    public function actionManaWxQr()
    {
        $this->display("wxqrcodelist");
    }//管理微信二维码

    public function actionInsertAliQr()
    {
        $this->display("addzfbqrcode");
    }//添加支付宝二维码

    public function actionManaAliQr()
    {
        $this->display("zfbqrcodelist");
    }//管理支付宝二维码

    public function actionOrderList()
    {
        $this->display("orderlist");
    }//订单列表

    public function actionApi()
    {
        $this->host = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://';

        $this->host.=$_SERVER['HTTP_HOST'];
        $this->display("api");
    }//Api说明

    public function actionMainBody()
    {
        $this->display("main");
    }//Api说明

    public function actionAppInsert()
    {
        $this->display("addapplication");
    }//添加应用

    public function actionAppMan()
    {
        $this->display("applicationlist");
    }//应用管理
}
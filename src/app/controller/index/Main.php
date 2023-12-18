<?php
/*
 * Copyright (c) 2023. Ankio.  由CleanPHP4强力驱动。
 */
/**
 * Package: app\controller\api_index
 * Class Main
 * Created By ankio.
 * Date : 2023/5/18
 * Time : 17:56
 * Description :
 */

namespace app\controller\index;

use app\database\dao\AppDao;
use app\database\dao\FileDao;
use app\database\dao\OrderDao;
use cleanphp\base\Config;
use cleanphp\base\Controller;
use library\login\LoginManager;


class Main extends Controller
{
    function robots(): string
    {
        return <<<EOF
User-agent: *

Disallow: /
EOF;
    }

    function file()
    {
        $file = filter_characters(get('file', ''));
        FileDao::getInstance()->get($file);
    }

    function pay(): string
    {

        $id = arg("id");

        $order = OrderDao::getInstance()->getByOrderIdWait($id);

        if (empty($order)) {
           return $this->render( 400, "该订单已支付或已关闭");
        }
        $app = AppDao::getInstance()->getByAppId($order->appid);
        if (empty($app)) {
            return $this->render( 400, "该订单已支付或已关闭");
        }
        return $this->render(200,null,[
            "app_image"=>$app->app_image,
            "app_name"=>$app->app_name,
            "app_item"=>$order->app_item,
            "image"=>$order->pay_image,
            "timeout"=>Config::getConfig("alipay")["validity_minute"],
            "mail"=>Config::getConfig("notice")["admin"],
            "price"=>$order->price,
            "create_time"=>date("Y-m-d H:i:s",$order->create_time),
            "start"=>$order->create_time,
            'real_price' => $order->real_price,
            'type'=>$order->pay_type
        ]);

    }

    function config()
    {
        return $this->render(200, null, [
            "languages" => [],
            "language" => '',
            "login" => LoginManager::init()->isLogin()
        ]);

    }



}
<?php

namespace app\controller\api_shop;

use Ankio\objects\PayCreateObject;
use Ankio\PayConfig;
use Ankio\Vpay;
use app\database\dao\ShopItemDao;
use cleanphp\base\Config;
use cleanphp\base\Controller;
use cleanphp\engine\EngineManager;

class Main extends Controller
{
    function create()
    {
        $id = arg("id");

       $item  = ShopItemDao::getInstance()->getById($id);

       if(empty($item)){
           return EngineManager::getEngine()->render(404,"不存在商品");
       }

        $pay_type = arg("pay_type");

        $config = new PayConfig(Config::getConfig("shop"));


        $order = new PayCreateObject();
        $order->app_item = $item->item_name;
        $order->appid = $config->id;
        $order->param = json_encode(array_merge(arg(),["item"=>$item->toArray()]));
        $order->price = $item->item_price;
        $order->pay_type =$pay_type;
        $order->notify_url = url("shop","main","notify");
        $order->return_url = url("shop","main","return");

        $pay = new Vpay($config);

        $result =$pay->create($order);

        if($result===false){
            return $this->render(500,$pay->getError());
        }
        return $this->render(200,"OK",$result->url);
    }


}
<?php
/*
 * Copyright (c) 2023. Ankio.  由CleanPHP4强力驱动。
 */

/**
 * Package: app\controller\shop
 * Class Main
 * Created By ankio.
 * Date : 2023/5/7
 * Time : 18:50
 * Description :
 */

namespace app\controller\shop;

use Ankio\PayConfig;
use Ankio\Vpay;
use app\database\dao\ShopCategoryDao;
use app\database\dao\ShopItemDao;
use cleanphp\base\Config;
use cleanphp\base\Controller;
use cleanphp\base\Response;
use cleanphp\engine\EngineManager;

class Main extends Controller
{
    public function __init()
    {
        if (!Config::getConfig("shop")['state']) {
            Response::location(url("index", "main", "index"));
        }
        EngineManager::getEngine()->setLayout("layout")->setArray(Config::getConfig("shop"))
            ->setData("image", Config::getConfig("login")["image"]);
    }

    function index()
    {
        $items = ShopItemDao::getInstance()->getAllItems();
        $category = ShopCategoryDao::getInstance()->getAllCategory();
        $shop = [];
        foreach ($category as $cate) {
            $shop_item = [];
            foreach ($items as $item) {
                if ($item['item_category'] == $cate['id']) {
                    $shop_item[] = $item;
                }
            }
            if (!empty($shop_item)) {
                $shop[$cate['name']] = $shop_item;
            }
        }
        EngineManager::getEngine()->setData("shop", $shop);
    }

    function item(): ?string
    {
        $id = arg('id');
        $item = ShopItemDao::getInstance()->getById($id);
        if (empty($item)) {
            return EngineManager::getEngine()->setLayout(null)->renderMsg(true, 404, "404 Not Found", "找不到该资源");
        }
        EngineManager::getEngine()->setData("args", arg())->setArray($item->toArray())->setData("inputs", empty($item->inputs)?[]:explode(",", $item->inputs))->setData("_id",$id);
        return null;
    }


    function return(): string
    {
        $config = new PayConfig(Config::getConfig("shop"));
        $pay = new Vpay($config);
        if($pay->payReturn()){
          return EngineManager::getEngine()->renderMsg(false,200,'支付成功',"您已成功支付，稍后将收到邮件提醒。",10,url(
               'shop','main','index'
           ),'返回商城');
        }
        return  EngineManager::getEngine()->renderMsg(true,500,'支付失败',$pay->getError(),10,url(
            'shop','main','index'
        ),'返回商城');
        
    }


}
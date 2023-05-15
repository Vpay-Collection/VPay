<?php
/*******************************************************************************
 * Copyright (c) 2022. Ankio. All Rights Reserved.
 ******************************************************************************/

/**
 * Package: app\controller\shop
 * Class Main
 * Created By ankio.
 * Date : 2023/5/7
 * Time : 18:50
 * Description :
 */

namespace app\controller\shop;

use app\database\dao\ShopCategoryDao;
use app\database\dao\ShopItemDao;
use cleanphp\base\Config;
use cleanphp\base\Controller;
use cleanphp\engine\EngineManager;

class Main extends Controller
{
    public function __init()
    {
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

        //   dumps($shop);

        EngineManager::getEngine()->setData("shop", $shop);


    }

    function item(): ?string
    {
        $id = arg('id');
        $item = ShopItemDao::getInstance()->getById($id);
        if (empty($item)) {
            return EngineManager::getEngine()->setLayout(null)->renderMsg(true, 404, "404 Not Found", "找不到该资源");
        }
        EngineManager::getEngine()->setData("args", arg())->setArray($item->toArray())->setData("inputs", explode(",", $item->inputs));
        return null;
    }
}
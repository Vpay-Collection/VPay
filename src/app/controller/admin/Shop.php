<?php
/*
 * Copyright (c) 2023. Ankio.  由CleanPHP4强力驱动。
 */

namespace app\controller\admin;

use app\controller\admin\BaseController;
use app\database\dao\FileDao;
use app\database\dao\ShopDao;
use app\database\model\ShopModel;
use cleanphp\base\Config;
use cleanphp\base\Request;
use cleanphp\file\File;
use library\database\object\Page;

class Shop extends BaseController
{
    private $config;

    public function __init(): ?string
    {
        $result = parent::__init();
        if ($result !== null) {
            return $result;
        }
        $this->config = Config::getConfig("shop");
        $demo = [
            'notice'=>'',
            'title'=>'内置商城',
            'logo'=>'',
            'state'=>0,
            'appid'=>'',
            'appkey'=>'',
            'host'=>Request::getAddress()
        ];
        if(empty( $this->config)){
            $this->config = [];
        }

        $this->config = array_merge($demo,$this->config);
        return null;
    }

    function config(): string
    {
        if (Request::isGet()) return $this->json(200, null, $this->config);
        foreach ($this->config as $key => &$value) {
            $value = post($key, $value);
        }
        Config::setConfig('shop', $this->config);
        return $this->json(200, "更新成功");
    }



    function items(): string
    {
        $page = new Page();
        $condition = [];
        if (!empty(arg("item"))) {
            $condition[] = "item like %:item_name%";
            $condition[':item_name'] = arg("item");
        }
        $result = ShopDao::getInstance()->getAll([], $condition,false, arg("page", 1), arg("size", 10), $page);
        return $this->render(200, null, $result, $page->total_count);
    }

    function addOrUpdateItem(): string
    {
        $model = new ShopModel(arg());
        if ($model->price < 0) {
            return $this->render(403, "金额不允许小于0");
        }
        if (empty($model->image)) {
            return $this->render(403, "图片不允许为空");
        }
        $model->inputs = trim($model->inputs, ",");

        if (empty($model->id) || $model->id == -1) {
            FileDao::getInstance()->use($model->image);
            ShopDao::getInstance()->insertModel($model);
        } else {
            $old = ShopDao::getInstance()->getById($model->id);
            FileDao::getInstance()->use($model->image,$old->image);
            ShopDao::getInstance()->updateModel($model);
        }
        return $this->render(200);
    }

    function delItem(): string
    {
        $item = ShopDao::getInstance()->getById(arg("id", 0));
        if (!empty($item)) {
            ShopDao::getInstance()->delById($item->id);
            FileDao::getInstance()->del($item->image);
        }
        return $this->render(200);
    }

}
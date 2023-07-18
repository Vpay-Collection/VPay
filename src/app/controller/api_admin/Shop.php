<?php
/*
 * Copyright (c) 2023. Ankio.  由CleanPHP4强力驱动。
 */

/**
 * Package: app\controller\api_admin
 * Class Notice
 * Created By ankio.
 * Date : 2023/5/6
 * Time : 13:24
 * Description :
 */

namespace app\controller\api_admin;

use app\database\dao\AppDao;
use app\database\dao\ShopCategoryDao;
use app\database\dao\ShopItemDao;
use app\database\model\AppModel;
use app\database\model\ShopCategoryModel;
use app\database\model\ShopItemModel;
use app\utils\ImageUpload;
use cleanphp\base\Config;
use cleanphp\base\Request;
use library\database\object\Page;
use library\mail\AnkioMail;

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
        return null;
    }

    /**
     * 处理配置信息
     * @return string
     */
    function config(): string
    {
        if (Request::isGet()) return $this->json(200, null, $this->config);
        foreach ($this->config as $key => &$value) {
            $value = post($key, $value);
        }
        Config::setConfig('shop', $this->config);
        return $this->json(200, "更新成功");
    }

    function category(): string
    {
        $page = new Page();
        $result = ShopCategoryDao::getInstance()->getAll([], [], arg("page", 1), arg("size", 10), $page);
        return $this->json(200, null, $result, $page->total_count);
    }

    function delCategory(): string
    {
        ShopCategoryDao::getInstance()->delById(arg("id", -1));
        return $this->json(200, "操作成功");
    }

    function addOrUpdateCategory(): string
    {
        $model = new ShopCategoryModel(arg());
        if (empty($model->id) || $model->id == -1) {
            ShopCategoryDao::getInstance()->insertModel($model);
        } else {
            ShopCategoryDao::getInstance()->updateModel($model);
        }
        return $this->render(200);
    }

    function items(): string
    {
        $page = new Page();

        $condition = [];
        if (!empty(arg("item_category"))) {
            $condition['item_category'] = arg("item_category", 0);
        }

        if (!empty(arg("name"))) {
            $condition[] = "item_name like %:item_name%";
            $condition['item_name'] = arg("name");
        }

        $result = ShopItemDao::getInstance()->getAll([], $condition, arg("page", 1), arg("size", 10), $page, false);

        return $this->render(200, null, $result, $page->total_count);
    }

    function addOrUpdateItem(): string
    {
        $model = new ShopItemModel(arg());
        if ($model->item_price < 0) {
            return $this->render(403, "金额不允许小于0");
        }
        if (empty($model->icon)) {
            return $this->render(403, "图片不允许为空");
        }

        $model->inputs = trim($model->inputs, ",");
        $image = new ImageUpload("shop");
        $model->icon = $image->useImage($model->icon);
        if (empty($model->id) || $model->id == -1) {
            ShopItemDao::getInstance()->insertModel($model);
        } else {

            $old = ShopItemDao::getInstance()->getById($model->id);
            if ($old->icon !== $model->icon) $image->delImage($old->icon);
            ShopItemDao::getInstance()->updateModel($model);
        }
        return $this->render(200);
    }

    function delItem(): string
    {
        $item = ShopItemDao::getInstance()->getById(arg("id", 0));
        if (!empty($item)) {
            ShopItemDao::getInstance()->delById($item->id);
            (new ImageUpload('shop'))->delImage($item->icon);
        }
        return $this->render(200);
    }


    function upload(): string
    {
        $image = new ImageUpload("shop");
        $filename = "";
        if ($image->upload($filename)) {
            return $this->render(200, null, $filename);
        }
        return $this->render(403, $filename);
    }

}
<?php
/*
 * Copyright (c) 2023. Ankio.  由CleanPHP4强力驱动。
 */

/**
 * Package: app\controller\api_admin
 * Class App
 * Created By ankio.
 * Date : 2023/5/6
 * Time : 14:01
 * Description :
 */

namespace app\controller\api_admin;

use app\database\dao\AppDao;
use app\database\dao\OrderDao;
use app\database\model\AppModel;
use app\utils\ImageUpload;
use library\database\object\Page;

class App extends BaseController
{
    function list(): string
    {
        $page = new Page();
        $result = AppDao::getInstance()->getAll([], [],false, arg("page", 1), arg("size", 10), $page);
        return $this->render(200, null, $result, $page->total_count);
    }

    function del(): string
    {
        $item = AppDao::getInstance()->getByAppId(arg("id", 0));
        if (!empty($item)) {
            AppDao::getInstance()->del($item->id);
            (new ImageUpload('app'))->delImage($item->app_image);
            OrderDao::getInstance()->delByAppid($item->id);
        }
        return $this->render(200);
    }

    function addOrUpdate(): string
    {
        $model = new AppModel(arg());
        if (empty($model->app_image)) {
            return $this->render(403, "图片不允许为空");
        }
        $image = new ImageUpload("app");

        $model->app_image = $image->useImage($model->app_image);

        if (empty($model->id) || $model->id == -1) {
            $model->app_key = rand_str(32);
            AppDao::getInstance()->insertModel($model);
        } else {
            $old = AppDao::getInstance()->getByAppId($model->id);
            if ($old->app_image !== $model->app_image) $image->delImage($old->app_image);
            $model->app_key = $old->app_key;
            AppDao::getInstance()->updateModel($model);
        }
        return $this->render(200);
    }

    function upload(): string
    {
        $image = new ImageUpload("app");
        $filename = "";
        if ($image->upload($filename)) {
            return $this->render(200, null, $filename);
        }
        return $this->render(403, $filename);
    }
}
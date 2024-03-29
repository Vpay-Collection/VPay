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

namespace app\controller\admin;

use app\database\dao\AppDao;
use app\database\dao\FileDao;
use app\database\dao\OrderDao;
use app\database\model\AppModel;
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
            FileDao::getInstance()->del($item->app_image);
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


        if (empty($model->id) || $model->id == -1) {
            $model->app_key = rand_str(32);
            FileDao::getInstance()->use($model->app_image);
            AppDao::getInstance()->insertModel($model);
        } else {
            $old = AppDao::getInstance()->getByAppId($model->id);
            FileDao::getInstance()->use($model->app_image,$old->app_image);
            $model->app_key = $old->app_key;
            AppDao::getInstance()->updateModel($model);
        }
        return $this->render(200);
    }

    function upload(): string
    {
        [$error, $name, $url] = FileDao::getInstance()->upload();
        if ($error)
            return $this->render(403, $error);
        return $this->render(200, null, $url);
    }
}
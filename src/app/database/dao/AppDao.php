<?php
/*
 * Copyright (c) 2023. Ankio.  由CleanPHP4强力驱动。
 */

/**
 * Package: app\database\dao
 * Class AppDao
 * Created By ankio.
 * Date : 2023/3/19
 * Time : 11:24
 * Description :
 */

namespace app\database\dao;

use app\database\model\AppModel;
use cleanphp\base\Config;
use cleanphp\base\Request;
use Couchbase\RegexpSearchQuery;
use library\database\object\Dao;

class AppDao extends Dao
{


    public function findImage($image): int|array
    {
        return $this->select()->where(['app_image like %:image%',':image'=>$image])->limit()->commit(false);
    }


    public function getByAppId($appid): ?AppModel
    {

        return $this->find(null, ['id' => $appid]);
    }


    public function del($id)
    {
        if ($id === 0) return;
        $this->delete()->where(['id' => $id])->commit();
    }


}